const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('st_received_quotations', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    quotation_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    vendor_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    quotation_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    delivery_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    remark: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    total_qty: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    total_tax: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    total_amt: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    total_tax_bid: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    total_amt_bid: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    status: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: true,
      defaultValue: "Y",
      comment: "R for check PO is revised or not"
    },
    created: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    }
  }, {
    sequelize,
    tableName: 'st_received_quotations',
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
    ]
  });
};
