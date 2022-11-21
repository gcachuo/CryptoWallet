import { useEffect, useState } from "react";
import { RefreshControl, ScrollView } from "react-native";
import numeral from "numeral";
import { Button, Card, Paragraph, Title } from "react-native-paper";

import UsersAPI, { IAmounts } from "../../API/Users";

import useAxiosInterceptors from "../../Hooks/useAxiosInterceptors";
import useAccessToken from "../../Hooks/useAccessToken";

export default function Cartera() {
  const accessToken = useAccessToken();
  const [amounts, setAmounts] = useState([] as IAmounts[]);
  const [show, setShow] = useState({} as { [index: string]: boolean });

  useAxiosInterceptors();

  function changeVisibility(book: string, status: boolean) {
    show[book] = status;
    setShow({ ...show });
  }

  useEffect(() => {
    setAmounts([]);
    fetchAmounts();
  }, []);

  function onRefresh() {
    fetchAmounts();
  }

  function fetchAmounts() {
    accessToken &&
      UsersAPI.fetchAmounts(accessToken).then((result) => {
        setAmounts(result);
      });
  }

  return (
    <ScrollView
      style={{ paddingHorizontal: 20 }}
      refreshControl={
        <RefreshControl refreshing={!amounts.length} onRefresh={onRefresh} />
      }
    >
      {!!amounts.length &&
        amounts
          .sort((a, b) => b.porcentaje - a.porcentaje)
          .map((row) => {
            const actual = +numeral(+row.precio * row.cantidad).format("#.##");
            let utilidad = +numeral(actual - row.costo).format("#.##");

            if (actual == 0) {
              return;
            }

            if (row.limite.venta > 0) {
              utilidad = +numeral(actual - row.limite.venta).format("#.##");
            }

            return (
              <Card key={row.book} style={{ marginVertical: 5 }}>
                <Card.Title
                  title={row.moneda}
                  subtitle={`Precio: ` + numeral(row.precio).format("$#,#.##")}
                />

                <Card.Content>
                  <Title>
                    {`Actual: ` + numeral(actual).format("$#,#.##")}
                  </Title>
                  <Title
                    style={{ color: utilidad > 0 ? "#3cb600" : "#c70000" }}
                  >
                    {`Porcentaje: ` + numeral(row.porcentaje).format("#.##%")}
                  </Title>
                  <Title
                    style={{ color: utilidad > 0 ? "#3cb600" : "#c70000" }}
                  >
                    {`Utilidad: ` + numeral(utilidad).format("$#,#.##")}
                  </Title>
                  {(show[row.book] ?? false) && (
                    <>
                      <Paragraph>
                        {`Cantidad: ` +
                          numeral(+row.cantidad).format("#,#.########")}
                      </Paragraph>
                      <Paragraph>
                        {`Precio Promedio: ` +
                          numeral(row.promedio).format("$#,#.##")}
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
                          {`Limite: ` +
                            numeral(row.limite.venta).format("$#,#.##")}
                        </Paragraph>
                      )}
                    </>
                  )}
                </Card.Content>
                <Card.Actions>
                  <Button
                    onPress={() => {
                      changeVisibility(row.book, !(show[row.book] ?? false));
                    }}
                  >
                    {show[row.book] ?? false ? "Hide" : "Show"}
                  </Button>
                </Card.Actions>
              </Card>
            );
          })}
    </ScrollView>
  );
}
