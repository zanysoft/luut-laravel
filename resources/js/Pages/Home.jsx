import Layout from "@/Layouts/Layout.jsx";
import {Head} from "@inertiajs/react";

export default function Home() {
    return (
        <Layout>
            <Head title={"test"}/>
            <h1>Home</h1>
		<div className="hidden">
            <section className="bg-gradient-to-r from-neutral-200 to-[#B0B4B9] md:pt-[76px] pt-[50px]">
                <div className="max-w-[1170px] mx-auto">
                    <div className="flex md:flex-nowrap flex-wrap items-start">
                        <div className="md:w-[42.5%] w-full px-[15px]">
                            <div className="md:text-end lg:mt-[115px] md:mt-14">
                                <h2 className="text-black text-[50px] font-bold leading-[60px]">
                                    Get paid <br/> to share
                                </h2>
                                <p className="leading-[30px] font-medium text-black text-[23px]">
                                    Earn money reposting <br/> for local businesses.
                                </p>
                            </div>
                        </div>
                        <div className="md:w-[57.6%] md:mt-0 mt-5 w-full px-[15px]">
                            <div className="text-center">
                                <img
                                    src="/images/hero-img.png"
                                    className="inline-block"
                                    alt=""
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section className="bg-[#ff0084] relative pt-[54px]">
                <div className="max-w-[1170px] mx-auto">
                    <div className="flex md:flex-nowrap flex-wrap items-start">
                        <div className="md:w-[42.5%] w-full px-[15px]">
                            <div className="md:text-end lg:mt-[148px] md:mt-14">
                                <h2 className="text-white sm:text-[50px] text-[45px] font-bold leading-[60px]">
                                    Find local <br/> opportunities.
                                </h2>
                                <p className="leading-[30px] font-medium text-white text-[23px]">
                                    Luut is where local businesses <br/> meet local influencers.
                                </p>
                            </div>
                        </div>
                        <div className="md:w-[57.6%] md:mt-0 mt-5 w-full px-[15px]">
                            <div>
                                <img src="/images/sec-2.png" alt=""/>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/*Section 3*/}
            <section className="bg-gradient-to-r from-neutral-200 to-[#B0B4B9] relative pt-3">
                <div className="container mx-auto">
                    <div className="grid grid-cols-1 md:grid-cols-2 items-center">
                        <div className="md:text-right text-center pt-5 md:pt-0 md:px-8 px-3">
                            <h2 className="md:text-[50px] text-[40px] font-bold leading-[70px]">
                                Earn money
                                <br/> re-posting.
                            </h2>
                            <p className="leading-[30px] font-medium text-[24px]">
                                And help local businesses
                                <br/>
                                meet new customers.
                            </p>
                        </div>
                        <div className="md:mt-0 mt-1">
                            <img className="max-w-full" src="/images/sec-3.webp" alt=""/>
                        </div>
                    </div>
                </div>
            </section>

            {/*Section 4*/}
            <section className="bg-blue-700 text-white relative pt-5 -mt-1">
                <div className="container mx-auto">
                    <div className="grid grid-cols-1 md:grid-cols-2 items-center">
                        <div className="md:text-right text-center pt-5 md:pt-0 md:px-8 px-3">
                            <h2 className="md:text-[50px] text-[40px] font-bold leading-[70px]">
                                Get the app <br/> for free.
                            </h2>
                            <p className="leading-[30px] font-medium text-[24px]">
                                Sign up for free and <br/> start earning right away!
                            </p>
                        </div>
                        <div className="md:mt-5 mt-3">
                            <img className="max-w-full" src="/images/sec-4.webp" alt=""/>
                        </div>
                    </div>
                </div>
            </section>

            {/*Section 5*/}
            <section className="bg-gradient-to-r from-neutral-200 to-[#B0B4B9] relative pt-5 -mt-1">
                <div className="container mx-auto">
                    <div className="grid grid-cols-1 md:grid-cols-2 items-center">
                        <div className="md:text-right text-center pt-5 md:pt-0 md:px-8 px-3">
                            <h2 className="md:text-[50px] text-[40px] font-bold leading-[70px]">
                                Earn $1 in <br/> 30 seconds.
                            </h2>
                            <p className="leading-[30px] font-medium text-[24px]">
                                Yes, it really is that easy!
                            </p>
                        </div>
                        <div className="md:mt-5 mt-3">
                            <img className="max-w-full" src="/images/sec-5.webp" alt=""/>
                        </div>
                    </div>
                </div>
            </section>

            {/*Section 6*/}
            <section className="bg-pink-600 relative pt-5 -mt-1">
                <div className="container mx-auto">
                    <div className="grid grid-cols-1 md:grid-cols-2 items-center">
                        <div className="md:text-right text-center pt-5 md:pt-0 md:px-8 px-3">
                            <img className="max-w-full" src="/images/sec-6-l.webp" alt=""/>
                        </div>
                        <div className="md:mt-5 mt-3">
                            <img className="max-w-full" src="/images/sec-6-r.webp" alt=""/>
                        </div>
                    </div>
                </div>
            </section>

            {/*Section 7*/}
            <section className="bg-blue-700 relative pt-5">
                <div className="container mx-auto">
                    <div className="grid grid-cols-1 md:grid-cols-2 items-center">
                        <div className="md:text-right text-center pt-5 md:pt-0 md:px-8 px-3">
                            <img className="max-w-full" src="/images/sec-7-l.webp" alt=""/>
                        </div>
                        <div className="md:mt-5 mt-3">
                            <img className="max-w-full" src="/images/sec-7-r.webp" alt=""/>
                        </div>
                    </div>
                </div>
            </section>

            {/*Section 8*/}
            <section className="bg-gradient-to-r from-neutral-200 to-[#B0B4B9] relative pt-5 -mt-1">
                <div className="container mx-auto">
                    <div className="grid grid-cols-1 md:grid-cols-2 items-center">
                        <div className="md:text-right text-center pt-5 md:pt-0 md:px-8 px-3">
                            <img className="max-w-full" src="/images/sec-8-l.webp" alt=""/>
                        </div>
                        <div className="md:mt-5 mt-3">
                            <img className="max-w-full" src="/images/sec-8-r.webp" alt=""/>
                        </div>
                    </div>
                </div>
            </section>

            {/*Section 9*/}
            <section className="bg-pink-600 relative pt-5 -mt-1">
                <div className="container mx-auto">
                    <div className="grid grid-cols-1 md:grid-cols-2 items-center">
                        <div className="md:text-right text-center pt-5 md:pt-0 md:px-8 px-3">
                            <img className="max-w-full" src="/images/sec-9-l.webp" alt=""/>
                        </div>
                        <div className="md:mt-5 mt-3">
                            <img className="max-w-full" src="/images/sec-9-r.webp" alt=""/>
                        </div>
                    </div>
                </div>
            </section>

            {/*Section 10*/}
            <section className="bg-gradient-to-r from-neutral-200 to-[#B0B4B9] relative pt-5 -mt-1">
                <div className="container mx-auto">
                    <div className="grid grid-cols-1 md:grid-cols-2 items-center">
                        <div className="md:text-right text-center pt-5 md:pt-0 md:px-8 px-3">
                            <img className="max-w-full" src="/images/sec-10-l.webp" alt=""/>
                        </div>
                        <div className="md:mt-5 mt-3">
                            <img className="max-w-full" src="/images/sec-10-r.webp" alt=""/>
                        </div>
                    </div>
                </div>
            </section>
            {/*Section 11*/}
            <section className="bg-blue-700 relative pt-5 -mt-1">
                <div className="container mx-auto">
                    <div className="grid grid-cols-1 md:grid-cols-2 items-center">
                        <div className="md:text-right text-center pt-5 md:pt-0 md:px-8 px-3">
                            <img className="max-w-full" src="/images/sec-11-l.webp" alt=""/>
                        </div>
                        <div className="md:mt-5 mt-3">
                            <img className="max-w-full" src="/images/sec-11-r.webp" alt=""/>
                        </div>
                    </div>
                </div>
            </section>
			</div>
        </Layout>
    );
}
