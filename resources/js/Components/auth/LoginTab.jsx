"use client";
import {useState} from "react";
import {FaRegEye, FaRegEyeSlash} from "react-icons/fa6";
import {Link, useForm} from "@inertiajs/react";
import Error from "@/Components/form/Error.jsx";


export default function LoginTab({onBack}) {
    const {data, setData, post, processing, errors, reset} = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const [showPass, setShowPass] = useState(false);
    const [isLoading, setIsLoading] = useState(false);

    const [error, setError] = useState({
        email: "",
        password: ""
    });

    const handleSubmit = (e) => {
        e.preventDefault();

        if (!data.email.length) {
            setError((p) => ({
                ...p,
                email: "Email can not be empty."
            }));
            return;
        }

        if (!data.password.length) {
            setError((p) => ({
                ...p,
                password: "Password can not be empty."
            }));
            return;
        }

        setError({
            email: "",
            password: ""
        });

        post(route('login'), {
            onSuccess: (e) => {
                console.log(e);
                reset('password')
            },
            onFinish: (e) => {
                console.log(e);
                //reset('password')
            },
        });
    };


    function handleLogin() {
        if (!data.password.length) {
            setError((p) => ({
                ...p,
                password: "Password can not be empty."
            }));
            return;
        }
        setIsLoading(true);
        setError({
            email: "",
            password: ""
        });
    }

    const handleChange = (e) => {
        const {name, value} = e.target;
        setData(name, value)

        const temp = error;
        temp[name] = "";
        setError(temp);
    };

    return (
        <div className="w-full step">
            <div className="mb-[30px]">
                <h2 className="text-2xl font-semibold text-black-2 mb-2.5">
                    Continue with your email
                </h2>
                <p className="text-gray-600 text-base">
                    Don't have an account?
                    <span className="underline">
            <Link href={route('register')}>Sign up</Link>
          </span>
                </p>
            </div>
            <form onSubmit={handleSubmit}>
                <ul className="flex flex-col gap-5">
                    <li className="flex flex-col gap-1">
                        <label htmlFor="email" className="text-gray-600">
                            Email
                        </label>
                        <input
                            className="text-base px-2.5 py-3 border border-red-1 outline-none text-gray-600 rounded-lg"
                            type="email"
                            name="email"
                            value={data.email}
                            onChange={handleChange}
                            placeholder="Enter Email"
                        />
                        <Error error={errors.email || error.email}/>
                    </li>
                    <li className="flex flex-col gap-1">
                        <label htmlFor="password-1" className="text-gray-600">
                            Password
                        </label>
                        <div className="relative w-full">
                            <input
                                className="text-base px-2.5 py-2.5 border w-full border-gray-1 outline-none text-gray-600 rounded-lg"
                                type={showPass ? "text" : "password"}
                                name="password"
                                value={data.password}
                                onChange={handleChange}
                                placeholder="Enter Password"
                            />
                            <button
                                id="password-to-text"
                                className="absolute right-3 top-2.5"
                                onClick={() => setShowPass(!showPass)}
                            >
                                {showPass ? <FaRegEyeSlash/> : <FaRegEye/>}
                            </button>
                        </div>
                        <Error error={errors.password || error.password}/>
                    </li>
                </ul>
                <button
                    id="submit-btn"
                    type="submit"
                    className="bg-blue-700 rounded-lg w-full text-white text-xl py-3 mb-3 border border-blue-1 mt-16"
                >
                    {isLoading ? "Logging..." : "Login"}
                </button>
                <div className="text-center">
                    <button
                        id="next-btn-1"
                        onClick={onBack}
                        type="button"
                        className="next-btn bg-blue-700 rounded-lg w-full text-white text-xl mb-3 py-3 border border-blue-1 mt-1"
                    >
                        Back
                    </button>
                </div>
            </form>
        </div>
    );
}
