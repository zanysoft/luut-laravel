import axios from "axios";
import { twMerge } from "tailwind-merge";
import clsx from "clsx";

export const env = process.env.NODE_ENV;

export const isProd = env === "production";

export function mergeClass(...inputs) {
  return twMerge(clsx(inputs));
}

export function apiUrl(path = "", params = {}) {
  let url = (
    env === "development" ? process.env.API_URL_LOCAL : process.env.API_URL
  ).replace(/\/+$/g, "");

  if (path) {
    if (url.indexOf("/api") !== -1 && path.indexOf("api/") !== -1) {
      path = path.replace("api/", "", path);
    }
    url += "/" + path.replace(/^\/+/g, "");
  }

  if (Object.keys(params).length) {
    url = appendUrlParam(url, params);
  }

  return url.replace(/([^:]\/)\/+/g, "$1");
}

export function baseURL(path = "", params = {}) {
  let url = (process.env.PUBLIC_URL ? process.env.PUBLIC_URL : "/").replace(
    /\/+$/g,
    ""
  );

  if (path) {
    url += "/" + path.replace(/^\/+/g, "");
  }
  if (Object.keys(params).length) {
    url = appendUrlParam(url, params);
  }
  return url.replace(/([^:]\/)\/+/g, "$1");
}

export function appendUrlParam(url, params = {}, prefix) {
  if (url && params) {
    const _url = new URL(url);
    Object.keys(params).forEach((key) => {
      if (typeof params[key] == "object") {
        Object.keys(params[key]).forEach((subKey) => {
          _url.searchParams.set(key + "[" + subKey + "]", params[key][subKey]);
        });
      } else {
        _url.searchParams.set(key, params[key]);
      }
    });
    url = _url.toString();
  }
  return url;
}

export const getIp = async () => {
  const res = await axios("https://api.ipify.org?format=json");
  return res.data.ip;
};

export function setCookie(name, value, days) {
  let expires = "";
  if (days) {
    const date = new Date();
    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
    expires = `; expires=${date.toUTCString()}`;
  }
  document.cookie = `${name}=${value || ""}${expires}; path=/`;
}

export function getCookie(name) {
  const nameEQ = `${name}=`;
  const ca = document.cookie.split(";");
  for (let i = 0; i < ca.length; i += 1) {
    let c = ca[i];
    while (c.charAt(0) === " ") c = c.substring(1, c.length);
    if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
  }
  return null;
}

export function removeCookie(name) {
  document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
}


/**
 * @param item
 */
export function isObject(item) {
  return (item && typeof item === "object" && !Array.isArray(item));
}

/**
 * @param item
 */
export function isArray(item) {
  return (item && Array.isArray(item));
}

export const updateState = (prevState, source, value = "") => {
  if (isObject(source)) {
    if (Object.keys(source).length) {
      let output = Object.assign({}, prevState);
      Object.keys(source).forEach(key => {
        let source_value = source[key];
        if (isObject(source_value)) {
          if (!(key in prevState)) {
            Object.assign(output, { [key]: source_value });
          } else {
            output[key] = updateState(prevState[key], source_value);
          }
        } else {
          if (!(isArray(source_value) && source_value.length == 0 && isObject(output[key]))) {
            Object.assign(output, { [key]: source_value });
          }
        }
      });
      return output;
    } else {
      return source;
    }
  } else if (typeof value == "object" && Object.keys(value).length) {
    return { ...prevState, [source]: { ...prevState[source], ...value } };
  } else {
    if (typeof value == "undefined") {
      throw "value parameter is required.";
    } else {
      if (source) {
        if (source.indexOf(".") > 0) {
          let parts = source.split(".");
          var [p1, p2, p3] = parts;
          if (parts.length == 3) {
            return {
              ...prevState, [p1]: { ...prevState[p1], [p2]: { ...prevState[p1][p2], [p3]: value } }
            };
          } else {
            return {
              ...prevState, [p1]: { ...prevState[p1], [p2]: value }
            };
          }
        } else {
          if (isArray(value) && value.length == 0 && isObject(prevState[source])) {
            return prevState;
          } else {
            return { ...prevState, [source]: value };
          }
        }
      } else {
        return prevState;
      }
    }
  }
};

export function validateEmail(email) {
  const regExp = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  if (!regExp.test(email)) {
    return false;
  }
  return true;
};
export const validateField = (field, value = null) => {
  const _type = field.type, _name = field.name, _required = field.required;

  let _valid = true;

  value = value ?? field.value ?? '';

  if (_required) {
    if (_type == "checkbox") {
      _valid = field.checked;
    } else if (!value.length || (["number", "string"].indexOf(typeof value) != -1 && value.replace(/ /g, "") == "")) {
      _valid = false;
    }
  }

  if (_valid && _type == "email" && !validateEmail(value)) {
    _valid = false;
  }

  if (_valid) {
    field.classList.remove("error");
  } else {
    field.classList.add("error");
  }

  return _valid;
};
