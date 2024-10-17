import axios from "axios";
import { apiUrl } from "../helper";

let menuCancelToken;
let hotelCancelToken;
let eventCancelToken;
let suggestionsCancelToken;
let globalSearchCancelToken;
let groupTripsCancelToken;
let prideCancelToken;

export async function calls(
  path,
  method,
  data,
  headers = null,
  params = null,
  cancelTokenProduct = null
) {
  let axiosOptions = {
    method,
    url: apiUrl(path),
    data,
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
      ...headers
    },
    params
  };
  if (cancelTokenProduct) {
    axiosOptions.cancelToken = cancelTokenProduct;
  }

  return await axios(axiosOptions);
  return await axios({
    method,
    url: apiUrl(path),
    data,
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
      ...headers
    },
    params,
    cancelToken: cancelTokenProduct
  });
}

export async function socialLogin(data) {
  try {
    return await calls("auth/login/social", "post", data, null);
  } catch (err) {
    return err;
  }
}

export async function deleteUser(id, token) {
  try {
    const res = await calls(`user/${id}`, "delete", null, {
      Authorization: `Bearer ${token}`
    });
    return res;
  } catch (err) {
    return err;
  }
}

export async function homepage(url) {
  const res = await calls(url, "get", null);
  return res;
}

export async function registration(url, data) {
  const res = await calls(url, "post", data);
  return res;
}

export async function login(url, data) {
  const res = await calls(url, "post", data);
  return res;
}

export async function tokenSignin(url, data) {
  const res = await calls(url, "post", data);
  return res;
}

export async function getCountries(url) {
  const res = await calls(url, "get");
  return res;
}

export async function getCities(url) {
  const res = await calls(url, "get");
  return res;
}

export async function getProfile(url, headers, params) {
  const res = await calls(url, "get", null, headers, params);
  return res;
}

// path,
// method,
// data,
// headers,
// params,
// cancelTokenProduct,
