const path = require('path');
const globule = require('globule');

const entries = {};
const srcFiles = ['./src/scripts/**/*.*', '!./src/scripts/**/_*.*', '!./src/scripts/components/**/*.*'];
globule.find(srcFiles).map((file) => {
  const key = file.replace("src/scripts/", "");
  entries[key] = file;
});

module.exports = {
  mode: "development",
  devtool: 'source-map',
  entry: entries,
  output: {
    // path: path.resolve(__dirname, 'scripts'),
    filename: '[name]'
  },
  module: {
    rules: [
      {
        test: /\.(jsx?)/,
        exclude: /node_modules/,
        use: [
          {
            loader: 'babel-loader',
            options: {
              presets: [
                ['@babel/preset-env', { 'targets': { "chrome": "58", "ie": "11"} }],
                '@babel/preset-react',
              ],
            },
          },
        ],
      },
    ]
  },
  resolve: {
    extensions: [
      '.js', '.jsx','.ts', '.tsx',
    ]
  }
};
