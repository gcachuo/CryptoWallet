import axios, { AxiosResponse } from "axios";
import { ApiResponse } from "../ApiResponse";

export interface IAmounts {
  book: string;
  cantidad: number;
  costo: number;
  estadisticas: {
    buy: number;
    sell: number;
    trades: {
      buy: number;
      cost: number;
      date: string;
      price: number;
      quantity: number;
      trade: number;
      type: "buy" | "sell";
    }[];
  };
  idMoneda: string;
  limite: { monto: number; venta: number };
  moneda: string;
  porcentaje: number;
  precio: number;
  promedio: number;
  total: number;
}

export default class UsersAPI {
  static async fetchAmounts(accessToken: string) {
    const response = (await axios.post("users/fetchAmounts", {
      user_token: accessToken,
    })) as AxiosResponse<
      ApiResponse<{
        amounts: IAmounts[];
      }>
    >;

    return response.data.data.amounts;
  }
}
