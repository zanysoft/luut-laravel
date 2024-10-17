export default function FieldError({ errors, name }) {
  return (
    <>
      {typeof errors[name] == "string" ?
        <p className="mt-0 text-error">{errors[name]}</p>
        : errors[name]?.length ? <p className="mt-0 text-error">{errors[name][0]}</p> : ""
      }
    </>
  );
}
