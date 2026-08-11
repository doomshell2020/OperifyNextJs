const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('st_goodsreceive', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    purchaseorder_id: {
      type: DataTypes.STRING(55),
      allowNull: false
    },
    vendor_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    store_id: {
      type: DataTypes.INTEGER,
      allowNull: false,
      defaultValue: 1
    },
    inwarddate: {
      type: DataTypes.DATEONLY,
      allowNull: false
    },
    bill_no: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    bill_date: {
      type: DataTypes.DATEONLY,
      allowNull: false
    },
    freight: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: false
    },
    total_qty: {
      type: DataTypes.FLOAT,
      allowNull: false
    },
    total_tax: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: false
    },
    total_amt: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: false
    },
    remark: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    created_date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    status: {
      type: DataTypes.ENUM('O','C'),
      allowNull: false,
      defaultValue: "C"
    },
    updated: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'st_goodsreceive',
    timestamps: false,
    freezeTableName: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
      {
        name: "id",
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
