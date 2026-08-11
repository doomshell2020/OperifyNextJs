const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('st_additem', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    category_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    uom: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    item_name: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    tax: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    itemtype: {
      type: DataTypes.ENUM('RawMaterial','FinishedProduct'),
      allowNull: true,
      defaultValue: "RawMaterial"
    },
    finishedprocess_id: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    productprocess_id: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    weight: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    volume: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    min_order_qty: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    cost_price: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    discount: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    size_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    location_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    item_isbn: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    sale_price: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    added_time: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    updated_time: {
      type: DataTypes.DATE,
      allowNull: true
    },
    status: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: false,
      defaultValue: "Y"
    },
    cname: {
      type: DataTypes.STRING(200),
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'st_additem',
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
        name: "ITEM_NAME",
        using: "BTREE",
        fields: [
          { name: "item_name" },
        ]
      },
      {
        name: "ITEM_NAME_2",
        using: "BTREE",
        fields: [
          { name: "item_name" },
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
