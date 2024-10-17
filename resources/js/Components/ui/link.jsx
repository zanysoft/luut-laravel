export default function Link({
  children,
  href,
  target,
  title,
  className,
  onClick,
  rel,
  disabled,
  ...rest
}) {
  return (
    <a
      href={href}
      target={target}
      title={title}
      className={className}
      onClick={onClick}
      rel={rel}
      disabled={disabled}
      {...rest}
    >
      {children}
    </a>
  );
}

// import NextLink from "next/link";

// export default function Link(props) {
//   return <NextLink {...props} />;
// }
