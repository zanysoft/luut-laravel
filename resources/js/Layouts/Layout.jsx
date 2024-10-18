import Header from "@/Components/layouts/Header.jsx";
import Footer from "@/Components/layouts/Footer.jsx";

export default function Layout({children}) {
    return (
        <>
            <Header/>
            {children}
            <Footer/>
        </>
    );
}
