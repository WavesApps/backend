<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserGoogle;
use App\Models\Superstar;
use App\Models\Payment;
use App\Models\PaymentBreakdown;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\SuperstarPost;
use App\Models\Subscribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard with real data summaries.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Ensure user is authenticated (middleware handles this, but double-check)
        if (!$user) {
            return redirect()->route('admin.login')
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]);
        }

        // Ensure only admin can access dashboard
        if ($user->role !== 'admin') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login')
                ->with('superstar_error', 'Please use the app to login. Web login is only available for administrators.')
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]);
        }

        // Get real data summaries
        $dashboardData = $this->getDashboardData();

        return response()
            ->view('adminpages.dashboard', $dashboardData)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Get comprehensive dashboard data.
     */
    private function getDashboardData()
    {
        // User Statistics
        $totalUsers = User::count();
        $totalGoogleUsers = UserGoogle::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();
        $newUsersToday = User::whereDate('created_at', today())->count();

        // Superstar Statistics
        $totalSuperstars = Superstar::count();
        $activeSuperstars = Superstar::where('status', 'active')->count();
        $newSuperstarsThisMonth = Superstar::whereMonth('created_at', now()->month)->count();
        $newSuperstarsToday = Superstar::whereDate('created_at', today())->count();

        // Payment Statistics
        $totalPayments = Payment::count();
        $totalRevenue = Payment::sum('total_amount');
        $todayRevenue = Payment::whereDate('created_at', today())->sum('total_amount');
        $thisMonthRevenue = Payment::whereMonth('created_at', now()->month)->sum('total_amount');
        
        // Payment breakdown
        $systemRevenue = PaymentBreakdown::sum('system_amount');
        $superstarRevenue = PaymentBreakdown::sum('superstar_amount');
        
        // Payment methods
        $paymentMethods = Payment::selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->get();
        
        // Payment status
        $paymentStatuses = Payment::selectRaw('payment_status as status, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('payment_status')
            ->get();

        // Content Statistics
        $totalPosts = SuperstarPost::count();
        $totalConversations = Conversation::count();
        $totalMessages = Message::count();
        $newPostsToday = SuperstarPost::whereDate('created_at', today())->count();
        $newMessagesToday = Message::whereDate('created_at', today())->count();

        // Subscription Statistics
        $totalSubscriptions = Subscribe::count();
        $activeSubscriptions = Subscribe::count(); // All subscriptions are considered active since there's no status field
        $newSubscriptionsThisMonth = Subscribe::whereMonth('created_at', now()->month)->count();

        // Recent Activities
        $recentPayments = Payment::with(['user', 'superstar'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        $recentUsers = User::orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        $recentSuperstars = Superstar::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Monthly Revenue Trend (last 6 months)
        $monthlyRevenue = Payment::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_amount) as total, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Top Superstars by Revenue
        $topSuperstars = Superstar::withSum('payments', 'total_amount')
            ->orderBy('payments_sum_total_amount', 'desc')
            ->take(5)
            ->get();

        return [
            // User Stats
            'totalUsers' => $totalUsers,
            'totalGoogleUsers' => $totalGoogleUsers,
            'newUsersThisMonth' => $newUsersThisMonth,
            'newUsersToday' => $newUsersToday,
            
            // Superstar Stats
            'totalSuperstars' => $totalSuperstars,
            'activeSuperstars' => $activeSuperstars,
            'newSuperstarsThisMonth' => $newSuperstarsThisMonth,
            'newSuperstarsToday' => $newSuperstarsToday,
            
            // Payment Stats
            'totalPayments' => $totalPayments,
            'totalRevenue' => $totalRevenue,
            'todayRevenue' => $todayRevenue,
            'thisMonthRevenue' => $thisMonthRevenue,
            'systemRevenue' => $systemRevenue,
            'superstarRevenue' => $superstarRevenue,
            'paymentMethods' => $paymentMethods,
            'paymentStatuses' => $paymentStatuses,
            
            // Content Stats
            'totalPosts' => $totalPosts,
            'totalConversations' => $totalConversations,
            'totalMessages' => $totalMessages,
            'newPostsToday' => $newPostsToday,
            'newMessagesToday' => $newMessagesToday,
            
            // Subscription Stats
            'totalSubscriptions' => $totalSubscriptions,
            'activeSubscriptions' => $activeSubscriptions,
            'newSubscriptionsThisMonth' => $newSubscriptionsThisMonth,
            
            // Recent Activities
            'recentPayments' => $recentPayments,
            'recentUsers' => $recentUsers,
            'recentSuperstars' => $recentSuperstars,
            
            // Analytics
            'monthlyRevenue' => $monthlyRevenue,
            'topSuperstars' => $topSuperstars,
        ];
    }
}

