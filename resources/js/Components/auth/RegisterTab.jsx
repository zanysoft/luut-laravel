"use client";
import {useState} from "react";
import {FaRegEye, FaRegEyeSlash} from "react-icons/fa6";
import Error from "@/Components/form/Error";
import {Link, useForm} from "@inertiajs/react";

export default function RegisterTab({onBack}) {
    const {data, setData, post, processing, errors, reset} = useForm({
        email: '',
        password: '',
        password_confirmation: '',
    });

    const [isLoading, setIsLoading] = useState(false);
    const [showPass, setShowPass] = useState(false);
    const [showPass2, setShowPass2] = useState(false);

    const [error, setError] = useState({
        email: "",
        password: "",
        password_confirmation: ""
    });



    const submit = (e) => {
        e.preventDefault();

        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    async function handleSubmit() {
        let _error = false;
        setError({
            email: "",
            password: "",
            password_confirmation: ""
        });

        if (!data.email.length) {
            setError((p) => ({...p, email: "Email can not be empty."}));
            _error = true;
        }
        if (!data.password.length) {
            setError((p) => ({...p, password: "Password can not be empty."}));
            _error = true;
        }
        if (!data.password_confirmation.length) {
            setError((p) => ({
                ...p,
                password_confirmation: "Confirm password can not be empty."
            }));
            _error = true;
        } else {
            if (data.password.length < 6) {
                setError((p) => ({
                    ...p,
                    password: "Password should have at least 6 characters."
                }));
                _error = true;
            } else if (data.password !== data.password_confirmation) {
                setError((p) => ({
                    ...p,
                    password: "Password and confirm password do not match.",
                    password_confirmation: "Password and confirm password do not match."
                }));
                _error = true;
            }
        }

        if (_error) {
            return;
        }

        setIsLoading(true);

        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    }

    const handleChange = (e) => {
        const {name, value} = e.target;
        setData(name, value)

        const temp = error;
        temp[name] = "";
        setError(temp);
    };

    const handleKeyDown = (e) => {
        if (e.key === "Enter") {
            handleSubmit();
        }
    };

    return (
        <div className="w-full step">
            <div className="mb-[30px]">
                <h2 className="text-2xl font-semibold text-black-2 mb-2.5">
                    Continue with your email
                </h2>
                <p className="text-gray-600 text-base">
                    Already have an account?
                    <span className="underline ">
                        <Link href="/LoginTab">Sign in</Link>
          </span>
                </p>
            </div>
            <div className="flex flex-col gap-3">
                <div className="flex flex-col gap-1">
                    <label className="text-gray-600"> Email </label>
                    <input
                        className={`px-2.5 py-3 border border-gray-1 outline-none text-gray-600 rounded-lg ${error?.email ? "border-error" : ""}`}
                        type="email"
                        name="email"
                        id="email"
                        onChange={handleChange}
                        onKeyDown={handleKeyDown}
                        value={data.email}
                        placeholder="Enter email"
                    />
                    <Error error={error.email || errors.email}/>
                </div>
                <div className="flex flex-col gap-1">
                    <label className="text-gray-600"> Password </label>
                    <div className="relative w-full">
                        <input
                            className={`text-base px-2.5 py-2.5 border w-full border-gray-1 outline-none text-gray-600 rounded-lg  ${error?.password ? "border-error" : ""}`}
                            type={showPass ? "text" : "password"}
                            name="password"
                            id="password"
                            onChange={handleChange}
                            onKeyDown={handleKeyDown}
                            value={data.password}
                            placeholder="Enter password"
                        />
                        <button
                            id="password-to-text"
                            className="absolute right-3 top-2.5"
                            onClick={() => setShowPass(!showPass)}
                        >
                            {showPass ? <FaRegEyeSlash/> : <FaRegEye/>}
                        </button>
                    </div>
                    <Error error={error.password || errors.password}/>
                </div>
                <div className="flex flex-col gap-1">
                    <label className="text-gray-600"> Confirm Password </label>
                    <div className="relative w-full">
                        <input
                            className={`text-base px-2.5 py-2.5 border w-full border-gray-1 outline-none text-gray-600 rounded-lg  ${error?.password ? "border-error" : ""}`}
                            type={showPass2 ? "text" : "password"}
                            name="password_confirmation"
                            id="password_confirmation"
                            onChange={handleChange}
                            onKeyDown={handleKeyDown}
                            value={data.password_confirmation}
                            placeholder="Enter password"
                        />
                        <button
                            id="password-to-text"
                            className="absolute right-3 top-2.5"
                            onClick={() => setShowPass2(!showPass2)}
                        >
                            {showPass2 ? <FaRegEyeSlash/> : <FaRegEye/>}
                        </button>
                    </div>
                    <Error error={error.password_confirmation}/>
                </div>
            </div>

            <button
                id="next-btn-1"
                onClick={() => handleSubmit()}
                type="button"
                className="next-btn bg-blue-700 rounded-lg w-full text-white text-xl mb-3 py-3 border border-blue-1 mt-10"
            >
                Continue
            </button>
            <button
                id="next-btn-1"
                onClick={onBack}
                type="button"
                className="next-btn bg-blue-700 rounded-lg w-full text-white text-xl mb-3 py-3 border border-blue-1 mt-1"
            >
                Back
            </button>
        </div>
    );
}
