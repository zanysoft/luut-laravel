"use client";
import { useEffect, useState } from "react";
import { updateState, validateField } from "@/utils/helper";
import axios from "axios";
import { useSession } from "next-auth/react";
import Errors from "@/app/componants/form/Errors";
import LoaderIcon from "@/app/componants/icon/loader";
import About from "@/app/componants/auth/inc/about";
import AboutBusiness from "@/app/componants/auth/inc/about-business";
import Address from "@/app/componants/auth/inc/address";
import Payment from "@/app/componants/auth/inc/payment";

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
      fb: "",
      ig: "",
      in: ""
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

export default function Profile({ onBack }) {
  const [isLoading, setIsLoading] = useState(false);
  const [currentTab, setTab] = useState(0);

  const [formData, setFormData] = useState(DefaultFormData);

  const [backendErrors, setBackendErrors] = useState({});

  const [error, setError] = useState({});
  const [sent, setSent] = useState({});

  const session = useSession();

  async function handleSubmit(callback) {
    let _error = false;
    setBackendErrors({});
    setError({});

    if (_error) {
      return;
    }
    setIsLoading(true);

    await axios({
      method: "POST",
      url: "/api/update-profile",
      headers: { token: session?.data?.user?.token },
      data: formData
    }).then((res) => {
      console.log(res);
      setSent(true);
      if (res.data?.business?.id) {
        setFormData((p) => updateState(p, "business.id"), res.data.business.id);
      }
      callback(res);
      setIsLoading(false);
    }).catch((error) => {
      console.log(error);
      if (error.response?.data?.errors) {
        setBackendErrors(error.response.data.errors);
      }
      if (error.response?.data?.message) {
        setBackendErrors((p) => ({ ...p, error: error.response.data.message }));
      }
      setIsLoading(false);
    });
  }

  const validateForm = (index) => {

    console.log(index);
    var count = 0;

    let fields = document.getElementById("tab-" + index).querySelectorAll("[required]");
    fields.forEach((field) => {
      if (!validateField(field)) {
        count++;
      }
    });

    if (count) {
      setError({ error: "Fill all required fields." });
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
    const { name, value } = e.target;
    temp[name] = "";
    setError(temp);
    setFormData((prevData) => updateState(prevData, name, value));
  };


  useEffect(() => {
    if (session?.status == "authenticated") {
      setIsLoading(true);
      axios({
        method: "GET",
        url: "/api/get-profile",
        headers: { token: session?.data?.user?.token }
      }).then((res) => {
        setIsLoading(false);
        if (res.status) {
          setFormData(res.data);
        }
      });
    }
  }, [session]);

  return (
    <div className="w-full step relative">
      <div className="mb-[30px]">
        <h2 className="text-2xl font-bold text-black-2 mb-2.5"> {steps[currentTab].title}</h2>
        <p className="text-gray-600 font-semibold text-base">{steps[currentTab].desc}</p>
        {backendErrors.error || error.error && <Errors error={backendErrors.error || error.error} />}
      </div>


      {currentTab == 0 &&
        <About
          handleChange={handleChange}
          error={error}
          backendErrors={backendErrors}
          formData={formData}
          setFormData={setFormData}
        />
      }

      {currentTab == 1 &&
        <AboutBusiness
          handleChange={handleChange}
          error={error}
          backendErrors={backendErrors}
          formData={formData}
          setFormData={setFormData}
        />
      }
      {currentTab == 2 &&
        <Address
          handleChange={handleChange}
          error={error}
          backendErrors={backendErrors}
          formData={formData}
          setFormData={setFormData}
        />
      }
      {currentTab == 3 &&
        <Payment
          handleChange={handleChange}
          error={error}
          backendErrors={backendErrors}
          formData={formData}
          setFormData={setFormData}
        />
      }
      <button
        id="next-btn-1"
        disabled={isLoading}
        onClick={handleNext}
        type="button"
        className="next-btn flex items-center justify-center bg-blue-700 rounded-lg w-full text-white text-xl mb-2 py-3 border border-blue-1 mt-10"
      >
        {isLoading && <LoaderIcon width={20} color="#ffffff" className="mr-2" />} Continue
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
