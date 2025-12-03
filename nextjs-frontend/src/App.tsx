import React, { useState } from 'react';
import { MessageSquare, Coins, Heart, TrendingUp, Share2, Users, Mail, Bell, Menu, X, Clipboard, Video, BookOpen } from 'lucide-react';
import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';
import Profile from './Profile';

function App() {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [activeTab, setActiveTab] = useState('Home');

  const navItems = ['Home', 'Courses', 'Study material', 'Profile', 'Daily works', 'Speaking sessions', 'Doubt clearance'];

  return (
    <div className="min-h-screen bg-[#F2F3F4]">
      {/* Navigation Bar */}
      <nav className="bg-white shadow-sm sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4">
          <div className="flex justify-between h-16">
            <div className="flex items-center">
              <img src="./images/logo.png" alt="Logo" className="h-12 w-auto" />
            </div>
            
            <div className="hidden md:flex items-center space-x-6">
              <div className="relative">
                <Coins className="w-5 h-5 text-orange-500 cursor-pointer hover:text-orange-600" />
                <span className="absolute -top-2 -right-2 px-2 py-0.5 bg-orange-500 text-white text-xs rounded-full">
                  30,931
                </span>
              </div>
              <div className="relative">
                <Bell className="w-5 h-5 text-gray-600 cursor-pointer hover:text-gray-700" />
                <span className="absolute -top-2 -right-2 px-1.5 py-0.5 bg-red-500 text-white text-xs rounded-full">
                  20
                </span>
              </div>
              <Mail className="w-5 h-5 text-gray-600 cursor-pointer hover:text-gray-700" />
              <button className="w-5 h-5 text-gray-600 cursor-pointer hover:text-gray-700">
                🔍
              </button>
              <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=32&h=32&fit=crop&crop=faces" alt="Profile" className="h-8 w-8 rounded-full cursor-pointer" />
            </div>

            {/* Mobile menu button */}
            <div className="md:hidden flex items-center">
              <button
                onClick={() => setIsMenuOpen(!isMenuOpen)}
                className="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-cyan-500"
                aria-expanded="false"
              >
                <span className="sr-only">Open main menu</span>
                {isMenuOpen ? (
                  <X className="block h-6 w-6" aria-hidden="true" />
                ) : (
                  <Menu className="block h-6 w-6" aria-hidden="true" />
                )}
              </button>
            </div>
          </div>
        </div>

        {/* Mobile menu */}
        {isMenuOpen && (
          <div className="md:hidden border-t border-gray-200">
            <div className="px-2 pt-2 pb-3 space-y-1">
              <div className="grid grid-cols-4 gap-4 p-4 border-b border-gray-200">
                <Share2 className="w-6 h-6 text-cyan-500 justify-self-center" />
                <Users className="w-6 h-6 text-cyan-500 justify-self-center" />
                <div className="relative justify-self-center">
                  <Coins className="w-6 h-6 text-orange-500" />
                  <span className="absolute -top-2 -right-2 px-2 py-0.5 bg-orange-500 text-white text-xs rounded-full">
                    30,931
                  </span>
                </div>
                <div className="relative justify-self-center">
                  <Bell className="w-6 h-6 text-gray-600" />
                  <span className="absolute -top-2 -right-2 px-1.5 py-0.5 bg-red-500 text-white text-xs rounded-full">
                    20
                  </span>
                </div>
              </div>
              {navItems.map((item) => (
                <Link
                  key={item}
                  to={item === 'Profile' ? '/profile' : '/'}
                  className={`block w-full px-3 py-2 rounded-md text-base font-medium ${
                    activeTab === item
                      ? 'bg-cyan-50 text-cyan-600'
                      : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                  }`}
                  onClick={() => {
                    setActiveTab(item);
                    setIsMenuOpen(false);
                  }}
                >
                  {item}
                </Link>
              ))}
            </div>
          </div>
        )}
      </nav>

      {/* Navigation Links - Only visible on desktop */}
      <div className="border-b bg-white sticky top-16 z-40 hidden md:block">
        <div className="max-w-7xl mx-auto">
          <div className="overflow-x-auto scrollbar-hide">
            <div className="flex whitespace-nowrap px-4 min-w-full">
              {navItems.map((item) => (
                <Link
                  key={item}
                  to={item === 'Profile' ? '/profile' : '/'}
                  className={`px-4 py-3 text-sm font-medium border-b-2 transition-colors duration-200 ${
                    activeTab === item
                      ? 'border-cyan-500 text-cyan-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  }`}
                  onClick={() => setActiveTab(item)}
                >
                  {item}
                </Link>
              ))}
            </div>
          </div>
        </div>
      </div>

      <Routes>
        <Route path="/profile" element={<Profile />} />
        <Route path="/" element={
          <>
            {/* Main Content */}
            <main className="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 bg-[#FFFFFF]">
              <h1 className="text-xl sm:text-2xl font-semibold text-black mb-6 sm:mb-8 px-2">
                Welcome to EdGuide
              </h1>

              <div className="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                {/* See what's going on */}
                <div className="bg-[#F5F5F5] rounded-lg shadow-sm overflow-hidden transform transition-transform duration-200 hover:scale-[1.02]">
                  <div className="bg-blue-500 p-4 text-white flex justify-between items-center">
                    <span className="text-base font-medium">See what's going on</span>
                    <span className="text-xl cursor-pointer hover:text-blue-100">⋮</span>
                  </div>
                  <div className="p-6 flex flex-col items-center">
                    <div className="w-20 h-20 bg-blue-100 rounded-lg flex items-center justify-center">
                      <div className="relative">
                        <MessageSquare className="w-12 h-12 text-blue-500 transform -rotate-6" strokeWidth={1.5} />
                        <MessageSquare className="w-12 h-12 text-blue-500 absolute top-1 left-1 transform rotate-6" strokeWidth={1.5} />
                      </div>
                    </div>
                    <p className="mt-4 text-base text-gray-600 text-center">See what's going on!</p>
                  </div>
                </div>

                {/* German Comics */}
                <div className="bg-[#F5F5F5] rounded-lg shadow-sm overflow-hidden transform transition-transform duration-200 hover:scale-[1.02]">
                  <div className="bg-green-500 p-4 text-white flex justify-between items-center">
                    <span className="text-base font-medium">German Comics</span>
                    <span className="text-xl cursor-pointer hover:text-green-100">⋮</span>
                  </div>
                  <div className="p-6 flex flex-col items-center">
                    <div className="w-20 h-20 bg-green-100 rounded-lg flex items-center justify-center">
                      <div className="relative">
                        <BookOpen className="w-12 h-12 text-green-500" strokeWidth={1.5} />
                      </div>
                    </div>
                    <p className="mt-4 text-base text-gray-600 text-center">Comics that cross boundaries!</p>
                  </div>
                </div>

                {/* Pre-Recorded Videos */}
                <div className="bg-[#F5F5F5] rounded-lg shadow-sm overflow-hidden transform transition-transform duration-200 hover:scale-[1.02]">
                  <div className="bg-orange-500 p-4 text-white flex justify-between items-center">
                    <span className="text-base font-medium">Pre-Recorded Videos</span>
                    <span className="text-xl cursor-pointer hover:text-orange-100">⋮</span>
                  </div>
                  <div className="p-6 flex flex-col items-center">
                    <div className="w-20 h-20 bg-orange-100 rounded-lg flex items-center justify-center">
                      <Video className="w-12 h-12 text-orange-500" strokeWidth={1.5} />
                    </div>
                    <p className="mt-4 text-base text-gray-600 text-center">Watch, rewatch and never miss a moment!</p>
                  </div>
                </div>

                {/* Take Assessment Test */}
                <div className="bg-[#F5F5F5] rounded-lg shadow-sm overflow-hidden transform transition-transform duration-200 hover:scale-[1.02]">
                  <div className="bg-teal-500 p-4 text-white flex justify-between items-center">
                    <span className="text-base font-medium">Take Assessment Test</span>
                    <span className="text-xl cursor-pointer hover:text-teal-100">⋮</span>
                  </div>
                  <div className="p-6 flex flex-col items-center">
                    <div className="w-20 h-20 bg-teal-100 rounded-lg flex items-center justify-center">
                      <Clipboard className="w-12 h-12 text-teal-500" strokeWidth={1.5} />
                    </div>
                    <p className="mt-4 text-base text-gray-600 text-center">Test today, triumph tomorrow!</p>
                  </div>
                </div>
              </div>
            </main>

            {/* Separator Section */}
            <div className="bg-gray-200 py-2"></div>

            {/* New Design Section */}
            <section className="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 bg-white flex items-center">
              <div className="flex-1">
                <h2 className="text-2xl font-bold mb-4 text-left">EdGuide - Linguist Since 1971</h2>
                <p className="text-gray-600">We Are The Best Choice For Your Dream</p>
              </div>
              <div className="flex-1">
                <div className="bg-orange-500 p-4 text-white rounded-lg">
                  <h3 className="text-lg font-semibold">Profile Completion</h3>
                  <p className="mt-2">Profile 80% complete. You are almost done!</p>
                  <button className="mt-4 bg-white text-orange-500 px-4 py-2 rounded">Complete Your Profile</button>
                </div>
              </div>
            </section>

            {/* Footer */}
            <footer className="bg-gray-800 text-white py-4 flex justify-between px-4">
              <p>Edguide Academy</p>
              <p>Terms & Privacy, © 2025 Copyright</p>
            </footer>
          </>
        } />
      </Routes>
    </div>
  );
}

export default App;