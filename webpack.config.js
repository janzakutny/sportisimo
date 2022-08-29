const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const webpack = require('webpack');

const ROOT_PATH = __dirname;

module.exports = {
    mode: "development",
    context: path.resolve(__dirname, 'src'),
    entry: {
        front: path.join(ROOT_PATH, "app/assets/front/entry.js")
    },

    output: {
        path: path.join(ROOT_PATH, "www/dist"),
        publicPath: "/dist/",
        filename: '[name].bundle.js',
        assetModuleFilename: 'images/[name][ext][query]'
    },

    module: {
        rules: [
            {
                test: /\.(s[ac]ss|css)$/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    "css-loader",
                    "sass-loader",
                ],
            },
            {
                test: /\.(png|svg|jpg|jpeg|gif)$/i,
                type: 'asset/resource'
            },
        ],
    },
    plugins: [
        new MiniCssExtractPlugin({
             // Options similar to the same options in webpackOptions.output
            // both options are optional
            filename: "[name].bundle.css",
            chunkFilename: "[id].bundle.css",
        }),
        new webpack.ProvidePlugin({
            $: "jquery",
            jQuery: "jquery",
            "window.jQuery": "jquery",
            "window.$": "jquery",
            naja: ['naja', 'default']
        })
    ]
};
