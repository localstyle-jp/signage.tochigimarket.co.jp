import React from 'react';

export const Buttton = ({children, color, className, onClick }) => {
  const newColor = color && "btn-" + color;
  const newClassName = "btn " + newColor + (className || '');

  return (
    <>
    <button
      className={newClassName}
      onClick={onClick}
    >{children}</button>
    </>
  );
};
