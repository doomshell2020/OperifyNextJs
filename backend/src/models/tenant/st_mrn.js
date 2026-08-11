const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('st_mrn', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    mrn_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    bill_challan_no: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    purchase_order_no: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    suppliername: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    bill_challan_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    transport_charges: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    other_charges: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    created: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'st_mrn',
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
