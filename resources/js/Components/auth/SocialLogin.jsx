"use client";

import {Link} from "@inertiajs/react";

export default function SocialLogin({ page, continueWithEmail }) {

  const popupCenter = (url, title) => {
    const dualScreenLeft = window.screenLeft ?? window.screenX;
    const dualScreenTop = window.screenTop ?? window.screenY;

    const width =
      window.innerWidth ??
      document.documentElement.clientWidth ??
      window.screen.width;

    const height =
      window.innerHeight ??
      document.documentElement.clientHeight ??
      window.screen.height;

    const systemZoom = width / window.screen.availWidth;

    const left = (width - 500) / 2 / systemZoom + dualScreenLeft;
    const top = (height - 550) / 2 / systemZoom + dualScreenTop;

    const newWindow = window.open(url, title,
      `width=${500 / systemZoom},height=${550 / systemZoom},top=${top},left=${left}`
    );

    newWindow?.focus();
  };
  return (
    <div className="w-full step active">
      {page == "login" ? (
        <div className="mb-[30px]">
          <h2 className="text-2xl font-semibold text-black-2 mb-2.5">
            Login your account
          </h2>
          <p className="text-gray-600 text-base">
            Don't have an account?{" "}
            <Link className="underline" href="/register">Sign up</Link>
          </p>
        </div>
      ) : (
        <div className="mb-[30px]">
          <h2 className="text-2xl font-semibold text-black-2 mb-2.5">
            Create your account
          </h2>
          <p className="text-gray-600 text-base">
            Already have an account?{" "}
            <Link className="underline" href="/LoginTab">Sign in</Link>
          </p>
        </div>
      )}
      <div>
        <ul className="flex flex-col gap-5 ">
          <li>
            <button
              type="button"
              onClick={() => popupCenter("/google-signin", "Google Signin")}
              className="border border-gray-1 w-full flex items-center rounded-lg p-[5px] text-black-1 text-base font-medium"
            >
              <img src="/images/google-icon.png" alt="no-img" />
              <span className="mx-auto">Continue with Google</span>
            </button>
          </li>
          <li>
            <button
              type="button"
              onClick={continueWithEmail}
              className="border border-gray-1 w-full flex items-center rounded-lg p-[5px] text-black-1 text-base font-medium"
            >
              <img src="/images/email-icon.png" alt="no-img" />
              <span className="mx-auto">Continue with Email</span>
            </button>
          </li>
        </ul>

        <div className="w-full flex items-center justify-center my-[50px]">
          <div className="w-full border-b border-gray-1"></div>
          <span className="px-2.5 text-gray-600">OR</span>
          <div className="w-full border-b border-gray-1"></div>
        </div>

        <ul className="flex flex-row gap-5  mb-6">
          <li className="w-1/2">
            <button
              type="button"
              onClick={() => popupCenter("/facebook-signin", "Facebook Signin")}
              className="border border-gray-1 w-full flex items-center rounded-lg p-[5px] text-black-1 text-base font-medium"
            >
              <img src="/images/facebook-icon.png" alt="no-img" />
              <span className="mx-auto">Facebook</span>
            </button>
          </li>
          <li className="w-1/2">
            <button
              type="button"
              onClick={() => popupCenter("/linkedin-signin", "Linkedin Signin")}
              className="border border-gray-1 w-full flex items-center rounded-lg p-[5px] text-black-1 text-base font-medium"
            >
              <img src="/images/linkedin-icon.png" alt="no-img" />
              <span className="mx-auto">Linkedin</span>
            </button>
          </li>
        </ul>
      </div>
    </div>
  );
}
