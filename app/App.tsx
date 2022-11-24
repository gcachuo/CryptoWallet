import "react-native-gesture-handler";
import { NavigationContainer } from "@react-navigation/native";
import { createDrawerNavigator } from "@react-navigation/drawer";

import { navigationRef } from "./RootNavigation";
import useAxiosInterceptors from "./Hooks/useAxiosInterceptors";

import Cartera from "./Screens/Cartera";
import Login from "./Screens/Login";

const Drawer = createDrawerNavigator();

export default function App() {
  useAxiosInterceptors();

  return (
    <NavigationContainer ref={navigationRef}>
      <Drawer.Navigator>
        <Drawer.Screen name="Cartera" component={Cartera} />
        <Drawer.Screen
          name="Login"
          component={Login}
          options={{ title: "Iniciar Sesión" }}
        />
      </Drawer.Navigator>
    </NavigationContainer>
  );
}
