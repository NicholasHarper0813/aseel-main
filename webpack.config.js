var path = require('path');
var ExtractTextPlugin = require("extract-text-webpack-plugin");

module.exports = 
{
    entry: 
    {
        app: './src/index.jsx'
    },
    output:
    {
        path: path.resolve(__dirname, 'dist'),
        filename: '[name].js'
    },
    module: 
    {
        rules: [
            {
                test: /\.scss$/,
                use: ExtractTextPlugin.extract({
                    fallback: 'style-loader',
                    use: ['css-loader','sass-loader'],
                    publicPath: 'dist'
                })
            },
            {
                test: /\.jsx?$/,
                exclude: /node_modules/,
                use: 'babel-loader'
            },
            {
                test: /\.(jpe?g|png|gif|svg)$/i,
                use: [
                    'file-loader?name=[name].[ext]&outputPath=images/&publicPath=wp-content/themes/aseel/dist/',
                    'image-webpack-loader'
                ]
            },
            { 
                test: 
                /\.(woff2?|svg)$/, 
                loader: 'url-loader?limit=10000&name=fonts/[name].[ext]' 
            },
            { 
                test: /\.(ttf|eot)$/, 
                loader: 'file-loader?name=fonts/[name].[ext]' 
            }
        ]
    },
    resolve: 
    {
        extensions: ['.js', '.jsx']
    },
    plugins: [
        new ExtractTextPlugin({
            filename: "style.css",
            allChunks: true
        })
    ]
}
