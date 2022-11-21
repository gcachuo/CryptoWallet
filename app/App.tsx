import "react-native-gesture-handler";
import { NavigationContainer } from "@react-navigation/native";
import { createDrawerNavigator } from "@react-navigation/drawer";
import Cartera from "./Screens/Cartera";
import useAxiosInterceptors from "./Hooks/useAxiosInterceptors";

const Drawer = createDrawerNavigator();

export default function App() {
  useAxiosInterceptors();
  
  return (
    <NavigationContainer>
      <Drawer.Navigator>
        <Drawer.Screen name="Cartera" component={Cartera} />
      </Drawer.Navigator>
    </NavigationContainer>
  );
}
