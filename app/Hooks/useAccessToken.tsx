import { useEffect, useState } from "react";
import AsyncStorage from "@react-native-async-storage/async-storage";

export default function useAccessToken(value?: string) {
  const [accessToken, setAccessToken] = useState(value);

  useEffect(() => {
    if (value) {
      setAccessToken(value);
      AsyncStorage.setItem("@access_token", value!);
    }
  }, [value]);

  useEffect(() => {
    if (!value) {
      setAccessToken("");
      AsyncStorage.getItem("@access_token").then((value1) => {
        value1 && setAccessToken(value1);
      });
    }
  }, [value]);

  return accessToken;
}
