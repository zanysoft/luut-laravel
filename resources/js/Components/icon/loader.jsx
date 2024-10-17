import React from "react";


export default function LoaderIcon({color = "#000079", type = 'circle', ...rest}) {
    return (
        <>
            {type == 'circle' && <svg  {...rest} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 45 45" stroke={color}>
                <g fill="none" fillRule="evenodd">
                    <g transform="translate(3 3)" strokeWidth="6">
                        <circle strokeOpacity=".3" cx="18" cy="18" r="18"/>
                        <path d="M36 18c0-9.94-8.06-18-18-18">
                            <animateTransform attributeName="transform" type="rotate" from="0 18 18" to="360 18 18" dur="1s" repeatCount="indefinite"/>
                        </path>
                    </g>
                </g>
            </svg>}
            {type == 'circle2' && <svg  {...rest} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
                <radialGradient id="a12" cx=".66" fx=".66" cy=".3125" fy=".3125" gradientTransform="scale(1.5)">
                    <stop offset="0" stopColor={color}></stop>
                    <stop offset=".3" stopColor={color} stopOpacity=".9"></stop>
                    <stop offset=".6" stopColor={color} stopOpacity=".6"></stop>
                    <stop offset=".8" stopColor={color} stopOpacity=".3"></stop>
                    <stop offset="1" stopColor={color} stopOpacity="0"></stop>
                </radialGradient>
                <circle transform-origin="center" fill="none" stroke="url(#a12)" strokeWidth="20" strokeLinecap="round" strokeDasharray="200 1000" strokeDashoffset="0" cx="100" cy="100" r="70">
                    <animateTransform type="rotate" attributeName="transform" calcMode="spline" dur="2" values="360;0" keyTimes="0;1" keySplines="0 0 1 1" repeatCount="indefinite"></animateTransform>
                </circle>
                <circle transform-origin="center" fill="none" opacity=".2" stroke={color} strokeWidth="20" strokeLinecap="round" cx="100" cy="100" r="70"></circle>
            </svg>}

            {type == 'clock' && <svg  {...rest} version="1.1" id="L2" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                                      viewBox="0 0 100 100" enableBackground="new 0 0 100 100">
                <circle fill="none" stroke={color} strokeWidth="6" strokeMiterlimit="10" cx="50" cy="50" r="45"/>
                <line fill="none" strokeLinecap="round" stroke={color} strokeWidth="6" strokeMiterlimit="10" x1="50" y1="50" x2="83" y2="50.5">
                    <animateTransform
                        attributeName="transform"
                        dur="2s"
                        type="rotate"
                        from="0 50 50"
                        to="360 50 50"
                        repeatCount="indefinite"/>
                </line>
                <line fill="none" strokeLinecap="round" stroke={color} strokeWidth="6" strokeMiterlimit="10" x1="50" y1="50" x2="49.5" y2="74">
                    <animateTransform
                        attributeName="transform"
                        dur="15s"
                        type="rotate"
                        from="0 50 50"
                        to="360 50 50"
                        repeatCount="indefinite"/>
                </line>
            </svg>}

            {type == 'dots' && <svg {...rest} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
                <circle fill={color} stroke={color} strokeWidth="25" r="15" cx="40" cy="65">
                    <animate attributeName="cy" calcMode="spline" dur="2" values="65;135;65;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="-.4"></animate>
                </circle>
                <circle fill={color} stroke={color} strokeWidth="25" r="15" cx="100" cy="65">
                    <animate attributeName="cy" calcMode="spline" dur="2" values="65;135;65;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="-.2"></animate>
                </circle>
                <circle fill={color} stroke={color} strokeWidth="25" r="15" cx="160" cy="65">
                    <animate attributeName="cy" calcMode="spline" dur="2" values="65;135;65;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="0"></animate>
                </circle>
            </svg>}
        </>
    )
}