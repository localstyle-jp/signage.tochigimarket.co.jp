import React from 'react';
import ReactDom from 'react-dom';

import { Buttton } from './components/Button';


const LoginButton = () => {
  const handleClick = () => {
    document.fm.submit();
  };
  return (
    <Buttton
      onClick={handleClick}
      className=""
      color="dark"
    >
      ログイン
    </Buttton>
  );
};

ReactDom.render(
  <LoginButton />,
  document.getElementById('login-button')
);
