export default function InputField({
                                     label,
                                     name,
                                     type,
                                     onChange,
                                     placeholder,
                                     className,
                                     autoComplete,
                                     value,
                                     reff,
                                     id,
                                     prefix,
                                     suffix,
                                     ...props
                                   }) {
  return (
    <div className="relative w-full">
      {label ? <label htmlFor="password-1" className="text-gray-600">
        {label}
      </label> : ""}
      <div className="flex">
        {prefix ? <div className="flex">{prefix}</div> : ""}
        <input
          type={type}
          name={name}
          id={id ?? name.replace('.','_')}
          placeholder={placeholder}
          onChange={onChange}
          className="text-base px-2.5 py-2.5 border w-full placeholder:text-3xl border-gray-1 outline-none text-gray-600 rounded-lg"
          value={value ?? ""}
          ref={reff}
          {...props}
        />
        {suffix ? <div className="flex">{suffix}</div> : ""}
      </div>
    </div>
  );
}
