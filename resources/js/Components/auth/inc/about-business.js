"use client";
import Error from "@/app/componants/form/Error";
import { FaFacebook, FaInstagram, FaLinkedin } from "react-icons/fa6";
import axios from "axios";
import { useEffect, useState } from "react";

export default function AboutBusiness({ formData, setFormData, handleChange, error, backendErrors }) {

  const [businessTypes, setBusinessTypes] = useState([]);

  async function fetchBusinessTypes() {
    try {
      await axios({
        url: ("/api/business-types"),
        //method: "post",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json"
        }
      }).then((res) => {
        if (res.status) {
          setBusinessTypes(res.data);
        }
      });
    } catch (e) {
      console.error(e.response);
    }
  }

  useEffect(() => {
    fetchBusinessTypes();
  }, []);

  return (
    <div className="flex flex-col gap-3" id="tab-1">
      <div className="flex flex-col gap-1">
        <label className="text-gray-600"> Business Name </label>
        <input
          className={`px-2.5 py-3 border border-gray-1 outline-none text-gray-600 rounded-lg ${error?.business_name ? "border-error" : ""}`}
          type="text"
          required
          name="business.name"
          id="business_name"
          onChange={handleChange}

          value={formData.business?.name}
          placeholder="Enter business name"
        />
        <Error error={error.business_name || backendErrors.business?.name} />
      </div>
      <div className="flex flex-col gap-1">
        <label className="text-gray-600"> Website </label>
        <input
          className={`px-2.5 py-3 border border-gray-1 outline-none text-gray-600 rounded-lg ${error?.business_website ? "border-error" : ""}`}
          type="url"
          name="business.website"
          id="business_website"
          onChange={handleChange}

          value={formData.business?.website}
          placeholder="Enter website"
        />
        <Error error={error.business_website || backendErrors.business_website} />
      </div>
      {businessTypes.length > 0 && <div className="flex flex-col gap-1">
        <label className="text-gray-600"> Industry Type </label>
        <select
          className={`text-base px-2.5 py-2.5 border w-full border-gray-1 outline-none text-gray-600 rounded-lg  ${error?.business_type ? "border-error" : ""}`}
          name="business.type_id"
          required
          id="business_type"
          onChange={handleChange}

          value={formData.business?.type_id}
        >
          <option value="">Select Type</option>
          {businessTypes.map((item, index) => <option key={index} value={item.id}>{item.name}</option>)}
        </select>
        <Error error={error.business_type || backendErrors.business_type} />
      </div>}

      <div className="mb-2">
        <strong>Social Accounts (Optional)</strong><br />
        Provide URLs of social profiles if you have any
      </div>

      <div className="flex items-center mb-1 gap-1">
        <div className="text-gray-600 w-[40px]">
          <FaFacebook className="text-3xl" />
        </div>
        <div className="w-full">
          <input
            className="px-2.5 py-3 w-full border border-gray-1 outline-none text-gray-600 rounded-lg"
            type="url"
            name="business.social_links.facebook"
            id="social_links_fb"
            onChange={handleChange}
            value={formData.business?.social_links?.facebook}
            placeholder="Enter facebook url"
          />
        </div>
      </div>
      <div className="flex items-center mb-1 gap-1">
        <div className="text-gray-600 w-[40px]">
          <FaInstagram className="text-3xl" />
        </div>
        <div className="w-full">
          <input
            className={`px-2.5 py-3 w-full border border-gray-1 outline-none text-gray-600 rounded-lg`}
            type="url"
            name="business.social_links.instagram"
            id="social_links_ig"
            onChange={handleChange}
            value={formData.business?.social_links?.instagram}
            placeholder="Enter instagram url"
          />
        </div>
      </div>
      <div className="flex items-center mb-1 gap-1">
        <div className="text-gray-600 w-[40px]">
          <FaLinkedin className="text-3xl" />
        </div>
        <div className="w-full">
          <input
            className={`px-2.5 py-3 w-full border border-gray-1 outline-none text-gray-600 rounded-lg`}
            type="url"
            name="business.social_links.linkedin"
            id="social_links_in"
            onChange={handleChange}
            value={formData.business?.social_links?.linkedin}
            placeholder="Enter linkedin url"
          />
        </div>
      </div>
    </div>
  );
}
