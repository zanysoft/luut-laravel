"use client";
import Error from "@/Components/form/Error.jsx";
import { PlaceAutocompleteClassic } from "@/Components/auth/inc/map.jsx";

export default function Address({ formData, setFormData, handleChange, error, backendErrors }) {
  return (
    <div className="flex flex-col gap-3" id="tab-2">

      <div className="flex flex-col gap-1">
        <label className="text-gray-600"> Address Line 1 </label>
        <input
          className={`px-2.5 py-3 border border-gray-1 outline-none text-gray-600 rounded-lg ${error?.business_address ? "border-error" : ""}`}
          type="text"
          required
          name="business.address"
          id="business_address"
          onChange={handleChange}

          value={formData.business?.address || ''}
          placeholder="Enter business address"
        />
        <Error error={error.business_address || backendErrors.business_address} />
      </div>
      <div className="flex flex-col gap-1">
        <label className="text-gray-600"> Address Line 2 </label>
        <input
          className={`px-2.5 py-3 border border-gray-1 outline-none text-gray-600 rounded-lg ${error?.business_address2 ? "border-error" : ""}`}
          type="text"
          name="business.address2"
          id="business_address2"
          onChange={handleChange}

          value={formData.business?.address2 || ''}
          placeholder="Enter business address2"
        />
        <Error error={error.business_address2 || backendErrors.business_address2} />
      </div>
      <div className="grid lg:grid-cols-3 gap-x-3">
        <div className="flex flex-col gap-1">
          <label className="text-gray-600"> City </label>
          <input
            className={`px-2.5 py-3 border border-gray-1 outline-none text-gray-600 rounded-lg ${error?.business_city ? "border-error" : ""}`}
            type="text"
            required
            name="business.city"
            id="business_city"
            onChange={handleChange}

            value={formData.business?.city || ''}
            placeholder="Enter city"
          />
          <Error error={error.business_city || backendErrors.business_city} />
        </div>
        <div className="flex flex-col gap-1">
          <label className="text-gray-600"> State </label>
          <input
            className={`px-2.5 py-3 border border-gray-1 outline-none text-gray-600 rounded-lg ${error?.business_state ? "border-error" : ""}`}
            type="text"
            name="business.state"
            id="business_state"
            onChange={handleChange}

            value={formData.business?.state || ''}
            placeholder="Enter state"
          />
          <Error error={error.business_state || backendErrors.business_state} />
        </div>
        <div className="flex flex-col gap-1">
          <label className="text-gray-600"> ZipCode </label>
          <input
            className={`px-2.5 py-3 border border-gray-1 outline-none text-gray-600 rounded-lg ${error?.business_zipcode ? "border-error" : ""}`}
            type="text"
            name="business.zipcode"
            id="business_zipcode"
            onChange={handleChange}

            value={formData.business?.zipcode || ''}
            placeholder="Enter zipcode"
          />
          <Error error={error.business_zipcode || backendErrors.business_zipcode} />
        </div>
      </div>
      <PlaceAutocompleteClassic />

      {/*<div className="mb-2">
          <strong>Business Address</strong>
        </div>*/}

    </div>
  );
}
