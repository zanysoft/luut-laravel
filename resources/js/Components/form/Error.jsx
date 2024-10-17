import { useEffect,useState } from "react";

export default function Error({ error }) {
  const [errors, setErrors] = useState([]);

  useEffect(()=>{
    if (typeof error == 'string'){
      setErrors([error])
    }else{
      setErrors(error)
    }
  },[error])
  return (
    <>
      {typeof error == "string" ?
        <p className="mt-0 text-error">{error}</p>
        : errors?.length ? <p className="mt-0 text-error">{errors[0]}</p> : ""
      }
    </>
  );
}
