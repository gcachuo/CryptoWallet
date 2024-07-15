import {
  createNavigationContainerRef,
  NavigationContainerRefWithCurrent,
} from "@react-navigation/native";

export const navigationRef =
  createNavigationContainerRef() as NavigationContainerRefWithCurrent<any>;

export function navigate(name: string, params?: any) {
  if (navigationRef.isReady()) {
    navigationRef.navigate(name, params);
  }
}
