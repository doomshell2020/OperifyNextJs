const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('job_challan_items', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    challan_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    item_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    quantity: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: false
    },
    uom_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    return_type: {
      type: DataTypes.ENUM('RawMaterial','FinishedProduct'),
      allowNull: true,
      defaultValue: 'RawMaterial'
    },
    rate: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true,
      defaultValue: 0.00
    },
    tax_rate: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true,
      defaultValue: 0.00
    },
    tax_amount: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true,
      defaultValue: 0.00
    },
    amount: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true,
      defaultValue: 0.00
    },
    total: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true,
      defaultValue: 0.00
    }
  }, {
    sequelize,
    tableName: 'job_challan_items',
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
      }
    ]
  });
};
