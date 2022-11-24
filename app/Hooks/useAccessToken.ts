import { useEffect, useState } from "react";

export default function useAccessToken() {
  const [accessToken, setAccessToken] = useState(
    "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2NjkwNjM4NTQsImRhdGEiOnsiaWQiOiJvdFF2a3ZMYldXZno3QUk5d1hGUTl5ZStBRHo4Rzc5ZklPcXVxVkk2d3BNPSIsIm5hbWUiOiJNZW1vIENhY2h1IiwiY29ycmVvIjoiZ2NhY2h1Lm9AZ21haWwuY29tIiwicGVyZmlsIjowfSwiZXhwIjoxNjY5MTUwMjU0fQ.uA1K0rvHWhnmGsV-lUnd6no5YT_m6_tHKFDtD6hI4mc"
  );

  useEffect(() => {}, []);

  return accessToken;
}
