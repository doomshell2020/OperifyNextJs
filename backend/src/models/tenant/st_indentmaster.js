const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('st_indentmaster', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    forward_to: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    approved_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    remark: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    cancel_by: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    cancel_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    po_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    po_status: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: false,
      defaultValue: "N"
    },
    edited: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: false,
      defaultValue: "N"
    },
    indent_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    item_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    size_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    sale_price: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    quantity: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    return_qty: {
      type: DataTypes.INTEGER,
      allowNull: true,
      defaultValue: 0
    },
    amount: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: false
    },
    indent_status: {
      type: DataTypes.ENUM('P','C'),
      allowNull: true,
      defaultValue: "P"
    },
    pace: {
      type: DataTypes.ENUM('F','A','S'),
      allowNull: true
    },
    status: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: false,
      defaultValue: "Y"
    },
    added_time: {
      type: DataTypes.DATE,
      allowNull: true
    },
    added_by: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    approved_by: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    updated_time: {
      type: DataTypes.DATE,
      allowNull: true
    },
    description: {
      type: DataTypes.TEXT,
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'st_indentmaster',
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
