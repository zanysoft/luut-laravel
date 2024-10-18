import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import {Head, Link, useForm} from '@inertiajs/react';
import Layout from "@/Layouts/Layout.jsx";
import SocialLogin from "@/Components/auth/SocialLogin.jsx";
import RegisterTab from "@/Components/auth/RegisterTab.jsx";
import LeftCol from "@/Components/auth/LeftCol.jsx";
import {useState} from "react";

export default function Register() {

    const [formStep, setFormStep] = useState(1);

    const submit = (e) => {
        e.preventDefault();

        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <Layout>
            <Head title="Register"/>
            <form onSubmit={submit}>
                <section className="container w-full lg:min-h-screen lg:flex items-center justify-center md:p-8 p-4 pt-8 pb-16">
                    <div className="w-full bg-white rounded-[20px] lg:h-[824px] flex lg:flex-row flex-col items-center lg:shadow-lg">
                        <LeftCol />
                        <div className="lg:px-[50px] lg:w-1/2 w-full overflow-hidden">
                            {formStep === 1 && (
                                <SocialLogin
                                    page="signup"
                                    continueWithEmail={() => {
                                        setFormStep(2);
                                    }}
                                />
                            )}
                            {(formStep === 2) && <RegisterTab onBack={() => setFormStep(1)} />}
                        </div>
                    </div>
                </section>
            </form>
        </Layout>
    );
}
