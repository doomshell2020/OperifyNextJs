const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('st_purchaseorderdetails', {
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
    poprimary_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    item_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    tax_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    item_amt: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    item_qty: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    item_base_price: {
      type: DataTypes.DOUBLE(14,2),
      allowNull: true
    },
    tax_percentage: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    item_tax_amt: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    item_total_amount: {
      type: DataTypes.DOUBLE(20,2),
      allowNull: true
    },
    created: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    uom: {
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
    vendor_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    inward_date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    revised_date: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'st_purchaseorderdetails',
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
