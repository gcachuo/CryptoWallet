import { Text, View } from "react-native";
import { useEffect, useState } from "react";
import UsersAPI, { IAmounts } from "../../API/Users";
import useAxiosInterceptors from "../../Hooks/useAxiosInterceptors";
import useAccessToken from "../../Hooks/useAccessToken";

export default function Cartera() {
  const accessToken = useAccessToken();
  useAxiosInterceptors();
  const [amounts, setAmounts] = useState([] as IAmounts[]);

  useEffect(() => {
    console.log(accessToken);
    accessToken &&
      UsersAPI.fetchAmounts(accessToken).then((result) => {
        setAmounts(result);
      });
  }, []);

  return (
    <View>
      <Text>Test</Text>
    </View>
  );
}
