"use client";
import {FaCheck} from "react-icons/fa6";
import {usePage} from "@inertiajs/react";

export default function Payment({formData, setFormData, handleChange, error, backendErrors}) {
    const packages = usePage().props.packages || [];

    function handlePackage(item) {
        let packages = formData.packages || [];
        const index = packages.indexOf(item.id);
        if (index == -1) {
            packages.push(item.id);
        } else {
            packages.splice(index, 1);
        }

        setFormData((p) => ({...p, packages: packages}));
    }

    return (
        <div className="flex flex-col gap-3" id="tab-3">
            <div className="grid grid-cols-3 gap-4 mb-3">
                {packages.length > 0 && packages.map((item) =>
                    <div className={`border-2 rounded text-center p-2 px-3 cursor-pointer relative ${formData.packages && formData.packages.indexOf(item.id) !== -1 ? "border-blue-900" : "border-black"}`}
                         onClick={() => handlePackage(item)}>
                        {formData.packages && formData.packages.indexOf(item.id) !== -1 &&
                            <FaCheck className="absolute top-1 right-1 border rounded-full bg-blue-900 text-white w-[20px] h-[20px] p-1"/>}
                        <div className="font-semibold">{item.title}</div>
                        <div className="font-medium">${item.amount}</div>
                    </div>
                )}
            </div>
            <div className="text-2xl font-semibold">Payment Options</div>
            <img src="/images/payment-logos.jpg"/>
        </div>
    );
}
