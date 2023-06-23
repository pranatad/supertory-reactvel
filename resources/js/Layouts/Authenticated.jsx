import React, { useState } from 'react'
import { ToastContainer } from 'react-toastify'
import { Link } from '@inertiajs/inertia-react'

import Dropdown from '@/Components/Dropdown'
import NavLink from '@/Components/NavLink'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink'
import AppLogo from '@/Components/AppLogo'
import FormUserModal from '@/Modals/FormUserModal'
import { useModalState } from '@/Hooks'

export default function Authenticated({ auth, header, children }) {
    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);

    const {isOpen, toggle} = useModalState(false)

    return (
        <div
            className="min-h-screen bg-gray-100"
            copyright="pranata.com"
            creator="pranata.com"
        >
            <nav className="bg-white border-b border-gray-100">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16">
                        <div className="flex">
                            <div className="shrink-0 flex items-center">
                                <Link href="/">
                                    <AppLogo />
                                </Link>
                            </div>

                            <div className="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink
                                    href={route('dashboard')}
                                    active={route().current('dashboard')}
                                >
                                    Dashboard
                                </NavLink>
                                <NavLink
                                    href={route('products.index')}
                                    active={route().current('products.index')}
                                >
                                    Barang
                                </NavLink>
                                <NavLink
                                    href={route('employees.index')}
                                    active={route().current('employees.index')}
                                >
                                    Karyawan
                                </NavLink>
                                <NavLink
                                    href={route('users.index')}
                                    active={route().current('users.index')}
                                >
                                    Users
                                </NavLink>
                                <NavLink
                                    href={route('payrolls.index')}
                                    active={route().current('payrolls.*')}
                                >
                                    Gaji
                                </NavLink>
                                <NavLink
                                    href={route('report')}
                                    active={route().current('report')}
                                >
                                    Laporan
                                </NavLink>
                            </div>
                        </div>

                        <div className="hidden sm:flex sm:items-center sm:ml-6">
                            <div className="ml-3 relative">
                                <NavLink
                                    
                                >
                                    {auth.user.name}
                                </NavLink>
                                <NavLink
                                    href={route('logout')}
                                    active={route().current('login')}
                                >
                                    Log Out
                                </NavLink>
                            </div>
                        </div>

                        <div className="-mr-2 flex items-center sm:hidden">
                            <button
                                onClick={() =>
                                    setShowingNavigationDropdown(
                                        (previousState) => !previousState
                                    )
                                }
                                className="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg
                                    className="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        className={
                                            !showingNavigationDropdown
                                                ? 'inline-flex'
                                                : 'hidden'
                                        }
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        className={
                                            showingNavigationDropdown
                                                ? 'inline-flex'
                                                : 'hidden'
                                        }
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    className={
                        (showingNavigationDropdown ? 'block' : 'hidden') +
                        ' sm:hidden'
                    }
                >
                    <div className="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink
                            href={route('dashboard')}
                            active={route().current('dashboard')}
                        >
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            href={route('products.index')}
                            active={route().current('products.index')}
                        >
                            Barang
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            href={route('employees.index')}
                            active={route().current('employees.index')}
                        >
                            Karyawan
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            href={route('users.index')}
                            active={route().current('users.index')}
                        >
                            Users
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            href={route('payrolls.index')}
                            active={route().current('payrolls.*')}
                        >
                            Gaji
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            href={route('report')}
                            active={route().current('report')}
                        >
                            Laporan
                        </ResponsiveNavLink>
                    </div>

                    <div className="pt-4 pb-1 border-t border-gray-200">
                        <div className="px-4" onClick={toggle}>
                            <div
                                className="font-medium text-base text-gray-800
                                "
                            >
                                {auth.user.name}
                            </div>
                            <div className="font-medium text-sm text-gray-500">
                                {auth.user.email}
                            </div>
                        </div>

                        <div className="mt-3 space-y-1">
                            <ResponsiveNavLink
                                method="post"
                                href={route('logout')}
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>
            {header && (
                <header className="bg-white shadow">
                    <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {header}
                    </div>
                </header>
            )}
            <main className="max-w-7xl mx-auto">{children}</main>
            <ToastContainer
                position="top-right"
                autoClose={5000}
                theme="colored"
                hideProgressBar={false}
                newestOnTop={false}
                closeOnClick
                rtl={false}
                pauseOnFocusLoss
                draggable
                pauseOnHover
            />
            <FormUserModal isOpen={isOpen} toggle={toggle} user={auth.user} />
        </div>
    )
}
