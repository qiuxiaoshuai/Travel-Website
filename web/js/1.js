// 引入mysql模块
const mysql = require('mysql');

// 创建一个连接对象
const connection = mysql.createConnection({
  host: 'localhost', // 数据库地址
  user: 'root', // 数据库用户
  password: '123456', // 数据库密码
  database: 'data' // 你要连接的数据库名
});

// 连接数据库
connection.connect((err) => {
  if (err) {
    console.error('连接失败: ' + err.stack);
    return;
  }

  console.log('连接成功，连接ID ' + connection.threadId);
});
