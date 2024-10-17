"use client";
import {useState} from "react";
import {updateState, validateField} from "@/utils/helper";
import LoaderIcon from "@/Components/icon/loader.jsx";
import About from "@/Components/auth/inc/about.jsx";
import AboutBusiness from "@/Components/auth/inc/about-business.jsx";
import Address from "@/Components/auth/inc/address.jsx";
import Payment from "@/Components/auth/inc/payment.jsx";
import Errors from "@/Components/form/Errors.jsx";
import {useForm, usePage} from "@inertiajs/react";

const DefaultFormData = {
    first_name: "",
    last_name: "",
    dob: "",
    gender: "",
    business: {
        name: "",
        website: "",
        type_id: "",
        social_links: {
            facebook: "",
            instagram: "",
            linkedin: ""
        }
    },
    packages: []
};

const steps = [
    {
        "name": "about",
        "title": "Share a bit about who you are",
        "desc": "Tell us your name, birthdate, and gender to personalize your Luut experience.",
        "required_fields": [
            "first_name"
        ]
    },
    {
        "name": "about-business",
        "title": "About Your Business",
        "desc": "Your website and social pages representing your business."
    },
    {
        "name": "business-location",
        "title": "Your Business Location",
        "desc": "Let us know the areas you serve."
    },
    {
        "name": "payment",
        "title": "Packages and Payment",
        "desc": "Choose Your Package."
    }
];

export default function ProfileTab({onBack}) {
    const props = usePage().props;
    const user = props.user;

    console.log(props);

    const {data, setData, post, processing, errors, reset} = useForm(user);

    const [isLoading, setIsLoading] = useState(false);
    const [currentTab, setTab] = useState(0);

    const [backendErrors, setBackendErrors] = useState({});

    const [error, setError] = useState({});
    const [sent, setSent] = useState({});

    async function handleSubmit(callback) {
        let _error = false;
        setBackendErrors({});
        setError({});

        if (_error) {
            return;
        }
        setIsLoading(true);

        post(route('register.profile'), {
            onSuccess: (res) => {
                callback(res);
            },
            onError: (error) => {
                console.log(error);
                if (error.response?.data?.errors) {
                    setBackendErrors(error.response.data.errors);
                }
                if (error.response?.data?.message) {
                    setBackendErrors((p) => ({...p, error: error.response.data.message}));
                }
                setIsLoading(false);
            },
            onFinish: () => setIsLoading(false),
        });
    }

    const validateForm = (index) => {
        var count = 0;
        let fields = document.getElementById("tab-" + index).querySelectorAll("[required]");
        fields.forEach((field) => {
            if (!validateField(field)) {
                count++;
            }
        });

        if (count) {
            setError({error: "Fill all required fields."});
        }
        return !count;
    };

    const handleNext = (e) => {
        setError({});
        var _tab = Math.min(currentTab + 1, (steps.length - 1));
        if (validateForm(currentTab)) {
            handleSubmit((res) => {
                setBackendErrors({});
                setTab(_tab);
            });
        }
    };
    const handleBack = (e) => {
        setError({});
        setTab(Math.max(currentTab - 1, 0));
    };
    const handleChange = (e) => {
        validateField(e.target);
        const temp = error;
        const {name, value} = e.target;
        setData((prevData) => updateState(prevData, name, value));

        temp[name] = "";
        setError(temp);
    };

    return (
        <div className="w-full step relative">
            <div className="mb-[30px]">
                <h2 className="text-2xl font-bold text-black-2 mb-2.5"> {steps[currentTab].title}</h2>
                <p className="text-gray-600 font-semibold text-base">{steps[currentTab].desc}</p>
                {backendErrors.error || error.error && <Errors error={backendErrors.error || error.error}/>}
            </div>


            {currentTab == 0 &&
                <About
                    handleChange={handleChange}
                    error={error}
                    backendErrors={backendErrors}
                    formData={data}
                    setFormData={setData}
                />
            }

            {currentTab == 1 &&
                <AboutBusiness
                    handleChange={handleChange}
                    error={error}
                    backendErrors={backendErrors}
                    formData={data}
                    setFormData={setData}
                />
            }
            {currentTab == 2 &&
                <Address
                    handleChange={handleChange}
                    error={error}
                    backendErrors={backendErrors}
                    formData={data}
                    setFormData={setData}
                />
            }
            {currentTab == 3 &&
                <Payment
                    handleChange={handleChange}
                    error={error}
                    backendErrors={backendErrors}
                    formData={data}
                    setFormData={setData}
                />
            }
            <button
                id="next-btn-1"
                disabled={isLoading}
                onClick={handleNext}
                type="button"
                className="next-btn flex items-center justify-center bg-blue-700 rounded-lg w-full text-white text-xl mb-2 py-3 border border-blue-1 mt-10"
            >
                {isLoading && <LoaderIcon width={20} color="#ffffff" className="mr-2"/>} Continue
            </button>
            {currentTab > 0 && <button
                id="next-btn-1"
                disabled={isLoading}
                onClick={handleBack}
                type="button"
                className="next-btn bg-blue-700 rounded-lg w-full text-white text-xl mb-3 py-3 border border-blue-1 mt-1"
            >
                Back
            </button>}
        </div>
    );
}
