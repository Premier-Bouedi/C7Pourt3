import SiteHeader from '../Components/SiteHeader';
import ShopFooter from '../Components/ShopFooter';
import WhatsAppButton from '../Components/WhatsAppButton';

export default function ShopLayout({ children }) {
    return (
        <div className="flex min-h-screen flex-col">
            <SiteHeader />
            <main className="flex-1">{children}</main>
            <ShopFooter />
            <WhatsAppButton />
        </div>
    );
}
