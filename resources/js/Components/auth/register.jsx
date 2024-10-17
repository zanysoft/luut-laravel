"use client";
import { useState } from "react";
import { FaRegEye, FaRegEyeSlash } from "react-icons/fa6";
import Link from "next/link";
import Error from "@/Components/form/Error";
import { signIn, useSession } from "next-auth/react";
import { useRouter } from "next/router";

const RegisterFormData = {
  first_name: "",
  email: "",
  password: "",
  confirm_password: ""
};

export default function Register({ onBack, setUser }) {

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");

  const [isLoading, setIsLoading] = useState(false);
  const [showPass, setShowPass] = useState(false);
  const [showPass2, setShowPass2] = useState(false);

  const [formData, setFormData] = useState(RegisterFormData);

  const [backendErrors, setBackendErrors] = useState({});

  const [error, setError] = useState({
    first_name: "",
    email: "",
    password: "",
    confirm_password: ""
  });

  async function handleRegistration() {
    let _error = false;
    setBackendErrors({});
    setError({
      email: "",
      password: "",
      confirm_password: ""
    });

    if (!email.length) {
      setError((p) => ({ ...p, email: "Email can not be empty." }));
      _error = true;
    }
    if (!password.length) {
      setError((p) => ({ ...p, password: "Password can not be empty." }));
      _error = true;
    }
    if (!confirmPassword.length) {
      setError((p) => ({
        ...p,
        confirm_password: "Confirm password can not be empty."
      }));
      _error = true;
    } else {
      if (password.length < 6) {
        setError((p) => ({
          ...p,
          password: "Password should have at least 6 characters."
        }));
        _error = true;
      } else if (password !== confirmPassword) {
        setError((p) => ({
          ...p,
          password: "Password and confirm password do not match.",
          confirm_password: "Password and confirm password do not match."
        }));
        _error = true;
      }
    }

    if (_error) {
      return;
    }
    setIsLoading(true);
    signIn("register", {
      redirect: false,
      email,
      password
    }).then((res) => {
      if (res.status == 200) {
        setIsLoading(false);
      } else if (res?.error) {
        const err = res.error.indexOf("{") !== -1 ? JSON.parse(res.error) : res.error;
        if (typeof err === "object") {
          setBackendErrors(err);
        } else {
          setBackendErrors({ others: res.error });
        }
      }
      setIsLoading(false);
    });

    /*axios
      .post(apiUrl("register"), { email,password })
      .then((res) => {
        console.log(res);
        setUser(res.data);
        setIsLoading(false);
      })
      .catch((error) => {
        setBackendErrors(
          error.response.data.errors
            ? error.response.data.errors
            : { others: [error.response.data.message] }
        );
        setIsLoading(false);
      });*/
  }

  const handleChange = (e) => {
    const { name, value } = e.target;
    if (name == "email") {
      setEmail(value);
    }
    if (name == "password") {
      setPassword(value);
    }
    if (name == "confirm_password") {
      setConfirmPassword(value);
    }

    const temp = error;
    temp[name] = "";
    setError(temp);
  };

  const handleKeyDown = (e) => {
    if (e.key === "Enter") {
      handleRegistration();
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
            value={email}
            placeholder="Enter email"
          />
          <Error error={error.email || backendErrors.email} />
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
              value={password}
              placeholder="Enter password"
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
        </div>
        <div className="flex flex-col gap-1">
          <label className="text-gray-600"> Confirm Password </label>
          <div className="relative w-full">
            <input
              className={`text-base px-2.5 py-2.5 border w-full border-gray-1 outline-none text-gray-600 rounded-lg  ${error?.password ? "border-error" : ""}`}
              type={showPass2 ? "text" : "password"}
              name="confirm_password"
              id="confirm_password"
              onChange={handleChange}
              onKeyDown={handleKeyDown}
              value={confirmPassword}
              placeholder="Enter password"
            />
            <button
              id="password-to-text"
              className="absolute right-3 top-2.5"
              onClick={() => setShowPass2(!showPass2)}
            >
              {showPass2 ? <FaRegEyeSlash /> : <FaRegEye />}
            </button>
          </div>
          <Error error={error.confirm_password} />
        </div>
      </div>

      <button
        id="next-btn-1"
        onClick={() => handleRegistration()}
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
