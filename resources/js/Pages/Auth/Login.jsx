"use client";
import {useEffect, useState} from "react";
import LoginTab from "@/Components/auth/LoginTab.jsx";
import LeftCol from "@/Components/auth/LeftCol.jsx";
import {usePage} from "@inertiajs/react";
import SocialLogin from "@/Components/auth/SocialLogin.jsx";

export default function Login() {
    const auth = usePage().props.auth;

    const [formStep, setFormStep] = useState(1);

    useEffect(() => {
        if (auth.check) {
            if (auth.user?.new) {
                router.push(`/register/profile`);
            } else {
                router.push(`/`);
            }
        }
    }, [auth]);

    return (
        <>
            <section className="container w-full lg:min-h-screen lg:flex items-center justify-center md:p-8 p-4 pt-8 pb-16">
                <div className="w-full bg-white rounded-[20px] lg:h-[824px] flex lg:flex-row flex-col items-center shadow-lg">
                    <LeftCol/>
                    <div className="lg:px-[50px] w-full overflow-hidden">
                        {formStep === 1 && (
                            <SocialLogin
                                page="login"
                                continueWithEmail={() => {
                                    setFormStep(2);
                                }}
                            />
                        )}

                        {/* Step 2 */}
                        {formStep === 2 && <LoginTab onBack={() => setFormStep(1)}/>}
                    </div>
                </div>
            </section>
        </>
    );
}
