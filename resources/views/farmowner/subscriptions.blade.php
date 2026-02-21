@extends('farmowner.layouts.app')

@section('title', 'Subscriptions')
@section('header', 'Subscription Plans')
@section('subheader', 'Manage your farm subscription')

@section('content')
                <!-- Subscription Required Alert -->
                @if(session('error'))
                <div class="bg-red-900/50 border border-red-600 text-red-300 p-4 rounded-lg mb-6">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🚫</span>
                        <p class="font-semibold">{{ session('error') }}</p>
                    </div>
                </div>
                @endif

                <!-- Current Subscription Status -->
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-bold text-white mb-4">Your Current Subscription</h3>
                    
                    @if(Auth::user()->farmOwner && Auth::user()->farmOwner->subscriptions()->where('status', 'active')->where('ends_at', '>', now())->exists())
                    @php
                    $active_sub = Auth::user()->farmOwner->subscriptions()->where('status', 'active')->where('ends_at', '>', now())->first();
                    $current_products = Auth::user()->farmOwner->products()->count();
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-green-900/30 p-4 rounded-lg border border-green-700">
                            <p class="text-sm text-gray-400 mb-2">Plan Type</p>
                            <p class="text-2xl font-bold text-green-600">{{ ucfirst($active_sub->plan_type ?? 'Standard') }}</p>
                        </div>
                        
                        <div class="bg-blue-900/30 p-4 rounded-lg border border-blue-700">
                            <p class="text-sm text-gray-400 mb-2">Status</p>
                            <p class="px-3 py-1 rounded font-semibold bg-green-900 text-green-300 w-fit">✓ Active</p>
                        </div>

                        <div class="bg-purple-900/30 p-4 rounded-lg border border-purple-700">
                            <p class="text-sm text-gray-400 mb-2">Products Used</p>
                            <p class="font-bold {{ $active_sub->product_limit && $current_products >= $active_sub->product_limit ? 'text-red-400' : 'text-purple-600' }}">
                                {{ $current_products }} / {{ $active_sub->product_limit ?? '∞' }}
                            </p>
                        </div>

                        <div class="bg-orange-900/30 p-4 rounded-lg border border-orange-700">
                            <p class="text-sm text-gray-400 mb-2">Days Remaining</p>
                            <p class="font-bold text-orange-600">{{ max(0, (int) now()->diffInDays($active_sub->ends_at, false)) }} days</p>
                        </div>
                    </div>

                    @if($active_sub->product_limit && $current_products >= $active_sub->product_limit)
                    <div class="mt-4 bg-yellow-900/30 p-4 rounded-lg border border-yellow-600">
                        <p class="text-yellow-400 font-semibold">⚠️ Product Limit Reached</p>
                        <p class="text-yellow-500 text-sm mt-1">You've used all {{ $active_sub->product_limit }} product slots. Upgrade your plan to add more products.</p>
                    </div>
                    @endif
                    @else
                    <div class="bg-yellow-900/30 p-4 rounded-lg border border-yellow-200">
                        <p class="text-yellow-700 font-semibold">⚠️ No Active Subscription</p>
                        <p class="text-yellow-600 text-sm mt-1">Upgrade to a paid plan to unlock premium features and reach more customers.</p>
                    </div>
                    @endif
                </div>

                <!-- Available Plans -->
                <div>
                    <h3 class="text-lg font-bold text-white mb-4">Available Plans</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Starter Plan -->
                        <div class="bg-gray-800 border border-gray-700 rounded-lg overflow-hidden hover:shadow-lg transition">
                            <div class="bg-blue-600 text-white p-6">
                                <h4 class="text-xl font-bold">Starter</h4>
                                <p class="text-blue-100 text-sm">For new farms</p>
                            </div>
                            <div class="p-6">
                                <div class="mb-6">
                                    <p class="text-3xl font-bold text-white">₱30<span class="text-sm text-gray-300">/month</span></p>
                                </div>
                                <ul class="space-y-3 mb-6">
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300"><strong class="text-white">Maximum 2 products</strong></span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">Up to 50 orders/month</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">Basic analytics</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">Email support</span>
                                    </li>
                                </ul>
                                <a href="{{ route('subscription.pay', ['plan' => 'starter']) }}" class="block w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded text-center">
                                    Subscribe Now
                                </a>
                            </div>
                        </div>

                        <!-- Professional Plan -->
                        <div class="bg-gray-800 border-2 border-green-600 rounded-lg overflow-hidden hover:shadow-lg transition relative">
                            <div class="absolute top-0 right-0 bg-yellow-500 text-black text-xs font-bold px-3 py-1 rounded-bl">POPULAR</div>
                            <div class="bg-green-600 text-white p-6">
                                <h4 class="text-xl font-bold">Professional</h4>
                                <p class="text-green-100 text-sm">Most popular</p>
                            </div>
                            <div class="p-6">
                                <div class="mb-6">
                                    <p class="text-3xl font-bold text-white">₱500<span class="text-sm text-gray-300">/month</span></p>
                                </div>
                                <ul class="space-y-3 mb-6">
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300"><strong class="text-white">Maximum 10 products</strong></span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">Up to 200 orders/month</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">Advanced analytics</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">Priority support</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">Marketing tools</span>
                                    </li>
                                </ul>
                                <a href="{{ route('subscription.pay', ['plan' => 'professional']) }}" class="block w-full py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded text-center">
                                    Subscribe Now
                                </a>
                            </div>
                        </div>

                        <!-- Enterprise Plan -->
                        <div class="bg-gray-800 border border-gray-700 rounded-lg overflow-hidden hover:shadow-lg transition">
                            <div class="bg-purple-600 text-white p-6">
                                <h4 class="text-xl font-bold">Enterprise</h4>
                                <p class="text-purple-100 text-sm">For large operations</p>
                            </div>
                            <div class="p-6">
                                <div class="mb-6">
                                    <p class="text-3xl font-bold text-white">₱1,200<span class="text-sm text-gray-300">/month</span></p>
                                </div>
                                <ul class="space-y-3 mb-6">
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300"><strong class="text-white">Unlimited products</strong></span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">Unlimited orders</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">Custom integrations</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">24/7 priority support</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 font-bold mr-2">✓</span>
                                        <span class="text-gray-300">Dedicated account manager</span>
                                    </li>
                                </ul>
                                <a href="{{ route('subscription.pay', ['plan' => 'enterprise']) }}" class="block w-full py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded text-center">
                                    Subscribe Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
@endsection
