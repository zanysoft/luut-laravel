import { useEffect, useState } from "react";

export default function Errors({ error }) {
  const [errors, setErrors] = useState([]);

  useEffect(() => {
    if (typeof error == "string") {
      setErrors([error]);
    } else {
      setErrors(error);
    }

  }, [error]);
  return (
    <>
      {typeof error == "string" ?
        <p className="my-2 text-error">{error}</p>
        : errors?.length ? <p className="mt-0 text-error font-medium mb-1">{errors[0]}</p> : ""
      }
    </>
  );
}
