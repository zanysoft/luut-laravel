import {FaApple, FaChevronDown, FaGooglePlay} from "react-icons/fa";
import {Link} from "@inertiajs/react";

export default function Footer() {


    return (
        <footer>
            <div className="bg-[#2E2E2E] md:pt-14 pt-5">
                <div className="max-w-[1170px] px-5 mx-auto">
                    <div className="md:flex border-b pb-16 border-white">
                        <div className="md:w-[43.7%] md:border-none border-b border-white w-full px-[15px]">
                            <div className="md:pl-10 md:pr-2.5">
                                <Link href={route('home')}>
                                    <img
                                        src="/images/footer-logo.svg"
                                        className="md:w-auto w-[250px]"
                                        alt=""
                                    />
                                </Link>
                            </div>
                        </div>
                        <div className="md:w-[24.7%] md:mt-0 mt-[30px] w-full px-[15px]">
                            <h6 className="text-[23px] mb-3 text-white font-medium underline-offset-4 leading-[30px] underline">
                                Quick Links
                            </h6>
                            <ul>
                                <li>
                                    <a href="#" className="text-xl font-normal text-white block">
                                        About Us
                                    </a>
                                </li>
                                <li>
                                    <a href="#" className="text-xl font-normal text-white block">
                                        Contact Us
                                    </a>
                                </li>
                                <li>
                                    <a href="#" className="text-xl font-normal text-white block">
                                        How It Works
                                    </a>
                                </li>
                                <li>
                                    <a href="#" className="text-xl font-normal text-white block">
                                        Affiliates
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div className="md:w-[24.7%] md:mt-0 mt-[30px] w-full px-[15px]">
                            <h6 className="text-[23px] mb-3 text-white font-medium underline-offset-4 leading-[30px] underline">
                                Other Links
                            </h6>
                            <ul>
                                <li>
                                    <a href="#" className="text-xl font-normal text-white block">
                                        Investor Relations
                                    </a>
                                </li>
                                <li>
                                    <a href="#" className="text-xl font-normal text-white block">
                                        Careers
                                    </a>
                                </li>
                                <li>
                                    <a href="#" className="text-xl font-normal text-white block">
                                        Media Kit
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div className="pt-10 md:pb-[60px] md:border-b md:border-white">
                        <h6 className="text-[23px] font-medium leading-[30px] text-white">
                            Follow Us on Social Media
                        </h6>
                        <ul className="flex md:max-w-full max-w-[175px] mx-auto md:flex-nowrap flex-wrap mt-[22px] items-center gap-[60px]">
                            <li>
                                <a href="#">
                                    <img src="/images/facebook.webp" alt=""/>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <img src="/images/instagram.webp" alt=""/>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <img src="/images/tiktok.webp" alt=""/>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <img src="/images/youtube.webp" alt=""/>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <img src="/images/linkdin.webp" alt=""/>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <img src="/images/twiter.webp" alt=""/>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div className="pt-[88px] pb-12">
                        <ul className="md:flex md:w-[74%] max-w-[842px] ml-auto justify-end items-center gap-10 mb-[73px]">
                            <li className="md:w-1/2 lg:mb-0 mb-5 w-full">
                                <a
                                    href="#"
                                    className="bg-neutral-300 text-neutral-800 w-full h-atuo block rounded-[10px] shadow-3xl px-5 py-[15px] transition-all ease-out duration-500 hover:-translate-y-2"
                                >
                                    <div className="font-bold text-[20px] flex items-center justify-center gap-x-1 ">
                                        <FaApple/> Download App <FaChevronDown/>
                                    </div>
                                </a>
                            </li>
                            <li className="md:w-1/2 w-full">
                                <a
                                    href="#"
                                    className="bg-neutral-300 text-neutral-800 w-full h-atuo block rounded-[10px] shadow-3xl px-5 py-[15px] transition-all ease-out duration-500 hover:-translate-y-2"
                                >
                                    <div className="font-bold text-[20px] flex items-center justify-center gap-x-1 ">
                                        <FaGooglePlay/> Download App <FaChevronDown/>
                                    </div>
                                </a>
                            </li>
                        </ul>
                        <p className="text-sm text-white font-normal leading-[18px]">
                            2022 luut®. All rights reserved. Privacy Policy | Terms and
                            Conditions | Sitemap
                        </p>
                    </div>
                </div>
            </div>
            <div className="bg-white pt-16 pb-12">
                <div className="max-w-[1190px] px-5 mx-auto">
                    <h6 className="text-base font-medium text-black leading-5 mb-5">
                        Luut, LLC | Privacy Policy
                    </h6>
                    <p className="text-base font-normal text-black">
                        Earnings disclaimer results may vary and testimonials are not
                        claimed to represent typical results. These results are meant as a
                        showcase of what the best, most motivated clients have done and
                        should not be taken as average or typical results. You should assume
                        that products, programs or personal recommendations made by luut,
                        may result in compensation paid to luut by those we recommend. We
                        recommend resources that we use ourselves, unless it specifically
                        states that we do not use that resource. We do recommend many
                        products and services which we do not use. If you would rather that
                        we not be compensated for these recommendations, go to Google and
                        search for the item and find a non-affiliate link to use. You should
                        perform your own due diligence and use your own best judgment prior
                        to making any investment decision pertaining to your business. By
                        virtue of visiting this site or interacting with any portion of this
                        site, you agree that you’re fully responsible for the investments
                        you make and any outcomes that may result.
                    </p>
                </div>
            </div>
        </footer>
    );
}
