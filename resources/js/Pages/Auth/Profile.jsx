"use client";
import LeftCol from "@/Components/auth/LeftCol.jsx";
import ProfileTab from "@/Components/auth/ProfileTab.jsx";
//import SocialLogin from "@/Components/auth/socialLogin";

export default function Profile() {
    return (
        <>
            <section className="container w-full lg:min-h-screen lg:flex items-center justify-center md:p-8 p-4 pt-8 pb-16">
                <div className="w-full bg-white rounded-[20px] lg:h-[824px] flex lg:flex-row flex-col items-center lg:shadow-lg">
                    <LeftCol/>
                    <div className="lg:px-[50px] lg:w-1/2 w-full overflow-hidden">
                        <ProfileTab/>
                    </div>
                </div>
            </section>
        </>
    );
}
