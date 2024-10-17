"use client";
import { useState } from "react";
import { signIn } from "next-auth/react";
import Link from "next/link";
import { FaRegEye, FaRegEyeSlash } from "react-icons/fa6";
import Error from "@/app/componants/form/Error";

const LgoinFormData = {
  email: "",
  password: ""
};

export default function Login({ onBack }) {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");

  const [showPass, setShowPass] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [loginData, setLoginData] = useState(LgoinFormData);
  const [backendErrors, setBackendErrors] = useState({});

  const [error, setError] = useState({
    email: "",
    password: ""
  });

  function handleLogin() {
    if (!password.length) {
      setError((p) => ({
        ...p,
        password: "Password can not be empty."
      }));
      return;
    }
    setBackendErrors({});
    setIsLoading(true);
    setError({
      email: "",
      password: ""
    });

    signIn("credentials", {
      redirect: false,
      email,
      password,
      origin: "login"
    }).then((res) => {
      console.log(res);
      if (res?.error) {
        const err = res.error.indexOf("{") !== -1 ? JSON.parse(res.error) : res.error;
        if (typeof err === "object") {
          setBackendErrors(err);
        } else {
          setBackendErrors({ others: res.error });
        }
      }
      setIsLoading(false);
    });
  }

  const handleChange = (e) => {
    const { name, value } = e.target;
    if (name == "email") {
      setEmail(value);
    }

    if (name == "password") {
      setPassword(value);
    }

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
            <Link href="/register">Sign up</Link>
          </span>
        </p>
      </div>

      <ul className="flex flex-col gap-5">
        <li className="flex flex-col gap-1">
          <label htmlFor="email" className="text-gray-600">
            Email
          </label>
          <input
            className="text-base px-2.5 py-3 border border-red-1 outline-none text-gray-600 rounded-lg"
            type="email"
            name="email"
            value={email}
            onChange={handleChange}
            placeholder="Enter Email"
          />
          <Error error={error.email || backendErrors.email} />
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
              value={password}
              onChange={handleChange}
              placeholder="Enter Password"
            />
            <button
              id="password-to-text"
              className="absolute right-3 top-2.5"
              onClick={() => setShowPass(!showPass)}
            >
              {showPass ? <FaRegEyeSlash /> : <FaRegEye />}
            </button>
          </div>
          <Error error={error.password || backendErrors.password} />
        </li>
      </ul>
      <button
        id="submit-btn"
        type="button"
        onClick={handleLogin}
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
    </div>
  );
}
