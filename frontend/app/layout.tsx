import type { Metadata } from "next";
import { Roboto } from "next/font/google";
import "./globals.css";
import { QueryProvider } from "../contexts/QueryProvider";
import { AuthProvider } from "../contexts/AuthContext";
import { PermissionProvider } from "../contexts/PermissionContext";

const roboto = Roboto({ subsets: ["latin"], weight: ['400', '500', '700'], variable: '--font-legacy-font' });

export const metadata: Metadata = {
  title: "Operify ERP",
  description: "Enterprise Resource Planning and Production Workflow Management System",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" className="h-full bg-slate-50 text-slate-900 antialiased" suppressHydrationWarning>
      <body className={`${roboto.className} ${roboto.variable} h-full flex flex-col`} suppressHydrationWarning>
        <QueryProvider>
          <AuthProvider>
            <PermissionProvider>
              {children}
            </PermissionProvider>
          </AuthProvider>
        </QueryProvider>
      </body>
    </html>
  );
}
