import { ScrollView } from "react-native";
import { useEffect, useState } from "react";
import UsersAPI, { IAmounts } from "../../API/Users";
import useAxiosInterceptors from "../../Hooks/useAxiosInterceptors";
import useAccessToken from "../../Hooks/useAccessToken";
import { Card, Paragraph, Title } from "react-native-paper";
import numeral from "numeral";

export default function Cartera() {
  const accessToken = useAccessToken();
  useAxiosInterceptors();
  const [amounts, setAmounts] = useState([] as IAmounts[]);

  useEffect(() => {
    accessToken &&
      UsersAPI.fetchAmounts(accessToken).then((result) => {
        setAmounts(result);
      });
  }, []);

  return (
    <ScrollView style={{ paddingHorizontal: 20 }}>
      {amounts.map((row) => {
        const actual = +numeral(+row.precio * row.cantidad).format("#.##");
        let utilidad = +numeral(actual - row.costo).format("#.##");

        if (actual == 0) {
          return;
        }

        if (row.limite.venta > 0) {
          utilidad = +numeral(actual - row.limite.venta).format("#.##");
        }

        return (
          <Card key={row.book} style={{ marginVertical: 10 }}>
            <Card.Title
              title={row.moneda}
              subtitle={`Precio: ` + numeral(row.precio).format("$#,#.##")}
            />

            <Card.Content>
              <Title>
                {`Porcentaje: ` + numeral(row.porcentaje).format("#.##%")}
              </Title>
              <Title>
                {`Utilidad: ` + numeral(utilidad).format("$#,#.##")}
              </Title>
              <Paragraph>
                {`Actual: ` + numeral(actual).format("$#,#.##")}
              </Paragraph>
              <Paragraph>
                {`Cantidad: ` + numeral(+row.cantidad).format("#,#.########")}
              </Paragraph>
              <Paragraph>
                {`Precio Promedio: ` + numeral(row.promedio).format("$#,#.##")}
              </Paragraph>
              <Paragraph>
                {`Ultima Compra: ` +
                  numeral(row.estadisticas.buy).format("$#,#.##")}
              </Paragraph>
              <Paragraph>
                {`Ultima Venta: ` +
                  numeral(row.estadisticas.sell).format("$#,#.##")}
              </Paragraph>
              <Paragraph>
                {`Costo: ` + numeral(row.costo).format("$#,#.##")}
              </Paragraph>
              {row.limite.venta > 0 && (
                <Paragraph>
                  {`Limite: ` + numeral(row.limite.venta).format("$#,#.##")}
                </Paragraph>
              )}
            </Card.Content>
          </Card>
        );
      })}
    </ScrollView>
  );
}
