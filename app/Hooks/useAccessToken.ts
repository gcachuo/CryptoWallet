import { useEffect, useState } from "react";
import AsyncStorage from "@react-native-async-storage/async-storage";

export default function useAccessToken(value?: string) {
  const [accessToken, setAccessToken] = useState(value);

  useEffect(() => {
    value && saveToken(value);
    if (!value) {
      AsyncStorage.getItem("@access_token").then((value1) => {
        value1 && setAccessToken(value1);
      });
    }
  }, [value]);

  async function saveToken(token?: string) {
    if (token) {
      setAccessToken(token);
      await AsyncStorage.setItem("@access_token", token);
    }
  }

  return accessToken;
}
