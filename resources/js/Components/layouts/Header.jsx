import {useEffect, useRef, useState} from "react";
import {Link, usePage} from "@inertiajs/react";
//import { signOut, useSession } from "next-auth/react";

export default function Header({menu}) {
    const isLoggedIn = usePage().props.auth.check;

    const session = useState({});

    const [isDropdownVisible, setIsDropdownVisible] = useState(false);

    const dropdownRef = useRef(null);

    const toggleDropdown = () => {
        setIsDropdownVisible(!isDropdownVisible);
    };
    const handleClickOutside = (event) => {
        if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
            setIsDropdownVisible(false);
        }
    };

    useEffect(() => {
        document.addEventListener("mousedown", handleClickOutside);
        return () => {
            document.removeEventListener("mousedown", handleClickOutside);
        };
    }, []);

    return (
        <header className={`bg-[#2E2E2E] md:py-5 py-2`}>
            <div className="container lg:py-6 md:py-3 mx-auto">
                <div
                    className="flex md:flex-nowrap flex-wrap md:justify-between justify-center items-center w-full md:gap-12">
                    <div className="flex items-center justify-between md:w-fit w-full">
                        <Link
                            href={route('home')}
                            className="inline-block lg:w-[250px] md:w-[150px] w-[150px] md:text-start text-center"
                        >
                            <img src="/images/logo.webp" className="m-0" alt=""/>
                        </Link>
                        <div
                            className="md:hidden flex items-center justify-center bg-neutral-300 hamburger ml-2 w-14 rounded-[10px] h-14 shadow-3xl cursor-pointer"
                            onClick={toggleDropdown}
                        >
                            <img src="/images/hamburger.svg" className="w-6 h-6" alt=""/>
                        </div>
                    </div>

                    {!isLoggedIn && <div
                        className="md:flex flex-col md:flex-row items-center hidden w-full md:m-0 mt-4 mb-2 md:gap-8 gap-3">
                        <Link
                            href={route("login")}
                            className="bg-neutral-300 flex-1 w-full h-atuo block rounded-[10px] shadow-3xl font-bold text-xl text-balck-1000 px-5 py-[15px] transition-all ease-out duration-500 hover:-translate-y-2 text-center"
                        >
                            Sign in
                        </Link>
                        <Link
                            href={route("register")}
                            className="bg-neutral-300 flex-1 w-full block rounded-[10px] shadow-3xl font-bold text-xl text-balck-1000 px-5 py-[15px] text-center transition-all ease-out duration-500 hover:-translate-y-2"
                        >
                            Sign up
                        </Link>
                    </div>}
                    <div>
                        <div
                            className="md:visible hidden bg-neutral-300 hamburger w-14 md:flex items-center justify-center rounded-[10px] h-14 shadow-3xl cursor-pointer"
                            onClick={toggleDropdown}
                        >
                            <img src="/images/hamburger.svg" className="w-6 h-6" alt=""/>
                        </div>
                    </div>
                </div>
            </div>
            <div
                ref={dropdownRef}
                className={`bg-neutral-300 shadow-2xl transition-all ease-out duration-500 -right-full ${isDropdownVisible ? "right-0" : ""} menu p-5 h-screen w-[340px] fixed top-0 z-[9999]`}
            >
                <div
                    className="absolute cross-icon top-4 right-4 cursor-pointer"
                    onClick={toggleDropdown}
                >
                    <img src="/images/cross-icon.svg" className="w-4" alt=""/>
                </div>
                <ul>
                    <li>
                        <Link
                            href={route('home')}
                            className="font-bold hover:text-blue-1000 inline-block py-4 text-xl text-balck-1000"
                        >
                            Home
                        </Link>
                    </li>
                    <li>
                        <Link
                            href="#"
                            className=" font-bold hover:text-blue-1000 inline-block py-4 text-xl text-balck-1000"
                        >
                            About Us
                        </Link>
                    </li>
                    {isLoggedIn ? <li>
                        <Link
                            href={route('logout')}
                            className=" font-bold hover:text-blue-1000 inline-block py-4 text-xl text-balck-1000"
                        >
                            Sign Out
                        </Link>
                    </li> : <>
                        <li className=" md:hidden ">
                            <Link
                                href={route('login')}
                                className=" font-bold hover:text-blue-1000 inline-block py-4 text-xl text-balck-1000"
                            >
                                Sign in
                            </Link>
                        </li>
                        <li className=" md:hidden ">
                            <Link
                                href={route("register")}
                                className=" font-bold hover:text-blue-1000 inline-block py-4 text-xl text-balck-1000"
                            >
                                Sign up
                            </Link>
                        </li>
                    </>}
                </ul>
            </div>
        </header>
    );
}
