import axios from "axios";
import { apiUrl } from "./helper";

export default async function getServerData(promises = []) {
  const menuPromise = axios({
    method: "get",
    url: apiUrl("menu"),
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
    },
  });
  const footerPromise = axios({
    method: "get",
    url: apiUrl("footer-links"),
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
    },
  });

  const [res, footer, ...result] = await Promise.allSettled([
    menuPromise,
    footerPromise,
    ...promises,
  ]);

  return {
    menu: res.value?.data?.data ?? null,
    footer: footer?.value?.data ?? null,
    layoutText:{},
    result,
  };
}
