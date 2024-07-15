import "react-native-gesture-handler";
import { NavigationContainer } from "@react-navigation/native";
import { createDrawerNavigator } from "@react-navigation/drawer";
import { createStackNavigator } from "@react-navigation/stack";

import { navigationRef } from "./RootNavigation";

import Cartera from "./Screens/Cartera";
import Login from "./Screens/Login";
import useAxiosInterceptors from "./Hooks/useAxiosInterceptors";

const Drawer = createDrawerNavigator();
const Stack = createStackNavigator();

export default function App() {
  useAxiosInterceptors();

  return (
    <NavigationContainer ref={navigationRef}>
      <Stack.Navigator screenOptions={{}}>
        <Stack.Screen
          name={"StackNavigator"}
          component={DrawerNavigator}
          options={{ headerShown: false }}
        />
        <Stack.Screen
          name="Login"
          component={Login}
          options={{ title: "Iniciar Sesión" }}
        />
      </Stack.Navigator>
    </NavigationContainer>
  );
}

function DrawerNavigator() {
  return (
    <Drawer.Navigator>
      <Drawer.Screen name="Cartera" component={Cartera} />
    </Drawer.Navigator>
  );
}
