"use client";
import Error from "@/app/componants/form/Error";

export default function About({ formData, setFormData, handleChange, error, backendErrors }) {
  return (
    <div className="flex flex-col gap-3" id="tab-0">
      <div className="grid grid-cols-2 gap-5">
        <div className="flex flex-col gap-1">
          <label className="text-gray-600"> First Name </label>
          <input
            className={`px-2.5 py-3 border border-gray-1 outline-none text-gray-600 rounded-lg ${error?.first_name ? "border-error" : ""}`}
            type="text"
            required
            name="first_name"
            id="first_name"
            onChange={handleChange}

            value={formData.first_name}
            placeholder="Enter name"
          />
          <Error error={error.first_name || backendErrors.first_name} />
        </div>
        <div className="flex flex-col gap-1">
          <label className="text-gray-600"> Last Name </label>
          <input
            className={`px-2.5 py-3 border border-gray-1 outline-none text-gray-600 rounded-lg ${error?.first_name ? "border-error" : ""}`}
            type="text"
            name="last_name"
            id="last_name"
            onChange={handleChange}

            value={formData.last_name}
            placeholder="Enter last name"
          />
          <Error error={error.first_name || backendErrors.first_name} />
        </div>
      </div>
      <div className="flex flex-col gap-1">
        <label className="text-gray-600"> Date of Birth </label>
        <input
          className={`px-2.5 py-3 border border-gray-1 outline-none text-gray-600 rounded-lg ${error?.dob ? "border-error" : ""}`}
          type="date"
          name="dob"
          id="dob"
          onChange={handleChange}
          value={formData.dob}
          placeholder="Select date"
        />
        <Error error={error.dob || backendErrors.dob} />
      </div>
      <div className="flex flex-col gap-1">
        <label className="text-gray-600"> Gender </label>
        <select
          required
          className={`text-base px-2.5 py-2.5 border w-full border-gray-1 outline-none text-gray-600 rounded-lg  ${error?.gender ? "border-error" : ""}`}
          name="gender"
          id="gender"
          onChange={handleChange}

          value={formData.gender}
        >
          <option value="">Select Gender</option>
          <option value="male">Male</option>
          <option value="female">Female</option>
        </select>
        <Error error={error.gender || backendErrors.gender} />
      </div>
    </div>
  );
}
