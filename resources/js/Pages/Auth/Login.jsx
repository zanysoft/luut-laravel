import {Head} from '@inertiajs/react';
import Layout from "@/Layouts/Layout.jsx";
import LeftCol from "@/Components/auth/LeftCol.jsx";
import SocialLogin from "@/Components/auth/SocialLogin.jsx";
import LoginTab from "@/Components/auth/LoginTab.jsx";
import {useState} from "react";

export default function Login() {
    const [formStep, setFormStep] = useState(1);
    return (
        <Layout>
            <Head title="Login"/>
            <section
                className="container w-full lg:min-h-screen lg:flex items-center justify-center md:p-8 p-4 pt-8 pb-16">
                <div
                    className="w-full bg-white rounded-[20px] lg:h-[824px] flex lg:flex-row flex-col items-center lg:shadow-lg">
                    <LeftCol/>
                    <div className="lg:px-[50px] lg:w-1/2 w-full overflow-hidden">
                        {formStep === 1 &&
                            <SocialLogin
                                page="login"
                                continueWithEmail={() => {
                                    setFormStep(2);
                                }}
                            />
                        }

                        {formStep === 2 &&
                            <LoginTab
                                onBack={() => setFormStep(1)}
                            />
                        }
                    </div>
                </div>
            </section>
        </Layout>
    );
}
