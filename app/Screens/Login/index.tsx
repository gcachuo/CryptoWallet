import { View } from "react-native";
import { Button, Surface, TextInput } from "react-native-paper";
import UsersAPI from "../../API/Users";
import { useCallback, useState } from "react";
import useAccessToken from "../../Hooks/useAccessToken";
import { useFocusEffect, useNavigation } from "@react-navigation/native";
import { DrawerNavigationProp } from "@react-navigation/drawer";

export default function Login() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [token, setToken] = useState("");

  const accessToken = useAccessToken(token);
  const navigation = useNavigation() as DrawerNavigationProp<any>;

  async function login() {
    const token = await UsersAPI.login(email, password);
    setToken(token);
  }

  useFocusEffect(
    useCallback(() => {
      console.log(accessToken);
      if (accessToken) {
        navigation.navigate("Cartera");
      }
    }, [accessToken])
  );

  return (
    <View>
      <View style={{ padding: 20 }}>
        <Surface style={{ padding: 20 }}>
          <TextInput
            label={"Correo"}
            keyboardType={"email-address"}
            textContentType={"emailAddress"}
            autoComplete={"email"}
            autoCapitalize={"none"}
            style={{ marginBottom: 20 }}
            onChangeText={(value) => {
              setEmail(value);
            }}
          />
          <TextInput
            label={"Contraseña"}
            secureTextEntry
            style={{ marginBottom: 20 }}
            onChangeText={(value) => {
              setPassword(value);
            }}
          />
          <Button
            mode={"contained"}
            onPress={() => {
              login();
            }}
          >
            Ingresar
          </Button>
        </Surface>
      </View>
    </View>
  );
}
